<?php

use App\Actions\ManageResortUsers;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

test('concurrent setup generation and redemption leave one token and allow one password write', function () {
    $directory = sys_get_temp_dir().'/jbr-concurrency-'.bin2hex(random_bytes(8));
    mkdir($directory, 0700);
    $database = $directory.'/fixtures.sqlite';
    touch($database);
    $mysqlSocket = getenv('JBR_MYSQL_QA_SOCKET');
    if ($mysqlSocket) {
        $socketDirectory = realpath(dirname($mysqlSocket));
        expect($socketDirectory)->not->toBeFalse();
        expect(basename($mysqlSocket))->toBe('mysql.sock');
        expect(is_link($mysqlSocket))->toBeFalse();
        expect(filetype($mysqlSocket))->toBe('socket');
        expect(in_array(dirname($socketDirectory), [realpath('/tmp'), realpath(sys_get_temp_dir())], true))->toBeTrue();
        expect(str_starts_with(basename($socketDirectory), 'jbr-mysql-fixture-'))->toBeTrue();
        expect(fileowner($socketDirectory))->toBe(posix_getuid());
        expect(fileperms($socketDirectory) & 0777)->toBe(0700);
        config(['database.default' => 'mysql', 'database.connections.mysql' => [
            'driver' => 'mysql', 'url' => null, 'host' => '127.0.0.1', 'port' => 0,
            'database' => 'jbr_copy_setup_fixture', 'username' => 'root', 'password' => '',
            'unix_socket' => $mysqlSocket, 'charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '', 'strict' => true, 'engine' => 'InnoDB',
        ]]);
    } else {
        config(['database.connections.sqlite.database' => $database]);
    }
    DB::purge();
    Artisan::call($mysqlSocket ? 'migrate:fresh' : 'migrate', ['--force' => true, '--no-interaction' => true]);
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
                } catch (AuthorizationException) {
                    $status = 'denied';
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
        expect(array_diff($statuses, $mysqlSocket ? ['generated', 'throttled'] : ['generated', 'throttled', 'busy']))->toBe([]);
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
        expect(array_diff($statuses, $mysqlSocket ? [Password::PASSWORD_RESET, Password::INVALID_TOKEN] : [Password::PASSWORD_RESET, Password::INVALID_TOKEN, 'busy']))->toBe([]);
        expect(DB::table('password_reset_tokens')->count())->toBe(0);
        expect(Hash::check('RacePassword042!', $target->fresh()->password) || Hash::check('RacePassword142!', $target->fresh()->password))->toBeTrue();
        $target->refresh()->forceFill(['password' => null])->save();
        $result = app(ManageResortUsers::class)->generateSetupLink($owner, $target);
        parse_str(parse_url($result['url'], PHP_URL_QUERY), $query);
        $this->travel(61)->seconds();
        $statuses = $runConcurrent(function (int $index) use ($owner, $target, $query): string {
            if ($index === 0) {
                app(ManageResortUsers::class)->generateSetupLink($owner, $target, 900);

                return 'generated';
            }

            return Password::broker('users')->reset([
                'email' => $target->email, 'token' => $query['token'], 'password' => 'RaceSetupPassword42!',
            ], function (User $user, string $password): void {
                $user->forceFill(['password' => $password])->save();
            });
        }, 'replacement-versus-redemption');
        $wins = count(array_filter($statuses, fn (string $status): bool => in_array($status, ['generated', Password::PASSWORD_RESET], true)));
        expect($wins)->toBe(1);
        expect(array_diff($statuses, $mysqlSocket ? ['generated', 'denied', Password::PASSWORD_RESET, Password::INVALID_TOKEN] : ['generated', 'denied', 'busy', Password::PASSWORD_RESET, Password::INVALID_TOKEN]))->toBe([]);
        $target->refresh();
        expect(DB::table('password_reset_tokens')->count())->toBe($target->password === null ? 1 : 0);
        expect(Password::broker('users')->tokenExists($target, $query['token']))->toBeFalse();
        expect($owner->fresh()->isSuperAdmin())->toBeTrue();
        $migration = require database_path('migrations/2026_10_13_165214_add_setup_expiry_to_password_reset_tokens_table.php');
        $tokenCount = DB::table('password_reset_tokens')->count();
        $migration->down();
        expect(Schema::hasColumn('password_reset_tokens', 'setup_expires_at'))->toBeFalse();
        expect(DB::table('password_reset_tokens')->count())->toBe($tokenCount);
        $migration->up();
        expect(Schema::hasColumn('password_reset_tokens', 'setup_expires_at'))->toBeTrue();
        expect(DB::table('password_reset_tokens')->whereNotNull('setup_expires_at')->count())->toBe(0);
    } finally {
        DB::disconnect();
        foreach (glob($directory.'/*') as $file) {
            unlink($file);
        }
        rmdir($directory);
    }
})->skip(! function_exists('pcntl_fork'), 'Requires local process support.');
