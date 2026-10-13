<?php

use App\Actions\ManageResortUsers;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

test('concurrent setup generation and redemption leave one token and allow one password write', function () {
    $directory = sys_get_temp_dir().'/jbr-concurrency-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    $database = $directory.'/fixtures.sqlite';
    touch($database);
    config(['database.connections.sqlite.database' => $database]);
    DB::purge();
    Artisan::call('migrate', ['--force' => true, '--no-interaction' => true]);
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => 'super_admin', 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    $target = User::factory()->create(['email' => 'race@example.invalid', 'is_staff' => true, 'resort_role' => 'admin', 'password' => null]);
    $runConcurrent = function (Closure $operation, string $phase) use ($directory): array {
        DB::disconnect();
        $children = [];
        foreach ([0, 1] as $index) {
            $pair = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
            $pid = pcntl_fork();
            if ($pid === 0) {
                fclose($pair[0]);
                fread($pair[1], 1);
                DB::purge();
                Password::clearResolvedInstance('auth.password');
                app()->forgetInstance('auth.password');
                try {
                    $status = $operation($index);
                } catch (ValidationException) {
                    $status = 'throttled';
                } catch (QueryException $exception) {
                    $status = str_contains($exception->getMessage(), 'database is locked') ? 'busy' : 'unexpected';
                } catch (Throwable $exception) {
                    $status = str_contains($exception->getMessage(), 'database is locked') ? 'busy' : 'unexpected:'.get_class($exception);
                }
                file_put_contents($directory.'/'.$phase.'-'.$index, $status);
                exit(0);
            }
            fclose($pair[1]);
            $children[] = [$pid, $pair[0]];
        }
        foreach ($children as [$pid, $pipe]) {
            fwrite($pipe, 'x');
            fclose($pipe);
        }
        foreach ($children as [$pid]) {
            pcntl_waitpid($pid, $status);
            expect(pcntl_wexitstatus($status))->toBe(0);
        }
        DB::purge();

        return [file_get_contents($directory.'/'.$phase.'-0'), file_get_contents($directory.'/'.$phase.'-1')];
    };
    try {
        $statuses = $runConcurrent(function () use ($owner, $target): string {
            app(ManageResortUsers::class)->generateSetupLink($owner, $target);

            return 'generated';
        }, 'generation');
        expect(array_count_values($statuses)['generated'] ?? 0)->toBe(1);
        expect(array_diff($statuses, ['generated', 'throttled', 'busy']))->toBe([]);
        expect(DB::table('password_reset_tokens')->count())->toBe(1);
        DB::table('password_reset_tokens')->delete();
        $result = app(ManageResortUsers::class)->generateSetupLink($owner, $target);
        parse_str(parse_url($result['url'], PHP_URL_QUERY), $query);
        $statuses = $runConcurrent(function (int $index) use ($target, $query): string {
            return Password::broker('users')->reset([
                'email' => $target->email, 'token' => $query['token'], 'password' => 'RacePassword'.$index.'42!',
            ], function (User $user, string $password): void {
                $user->forceFill(['password' => $password])->save();
            });
        }, 'redemption');
        expect(array_count_values($statuses)[Password::PASSWORD_RESET] ?? 0)->toBe(1);
        expect(array_diff($statuses, [Password::PASSWORD_RESET, Password::INVALID_TOKEN, 'busy']))->toBe([]);
        expect(DB::table('password_reset_tokens')->count())->toBe(0);
        expect(Hash::check('RacePassword042!', $target->fresh()->password) || Hash::check('RacePassword142!', $target->fresh()->password))->toBeTrue();
    } finally {
        DB::disconnect();
        foreach (glob($directory.'/*') as $file) {
            unlink($file);
        }
        rmdir($directory);
    }
})->skip(! function_exists('pcntl_fork'), 'Requires local process support.');
