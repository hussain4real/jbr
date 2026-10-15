<?php

namespace App\Console\Commands;

use App\Actions\ManageResortUsers;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use SensitiveParameter;
use Throwable;

class PrepareResortAccountSetup extends Command
{
    protected $signature = 'website:account-setup {email : Exact approved new account email}
        {--actor-email= : Existing active super administrator approving creation}
        {--name= : New account holder name}
        {--role= : Explicit role; only admin is supported}
        {--setup-file= : Exclusive private handoff filename ending in .link}';

    protected $description = 'Create an approved administrator without a password or email, with private one-use setup';

    public function handle(ManageResortUsers $users): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Private administrator setup requires interactive confirmation.');

            return self::FAILURE;
        }
        try {
            $data = Validator::make([
                'email' => $this->argument('email'), 'actor_email' => $this->option('actor-email'),
                'name' => $this->option('name'), 'resort_role' => $this->option('role'),
                'filename' => $this->option('setup-file'),
            ], [
                'email' => ['required', 'email', 'max:254'], 'actor_email' => ['required', 'email', 'max:254'],
                'name' => ['required', 'string', 'max:255'], 'resort_role' => ['required', Rule::in([User::ROLE_ADMIN])],
                'filename' => ['required', 'string', 'regex:/\A[a-zA-Z0-9][a-zA-Z0-9_-]{0,63}\.link\z/'],
            ])->validate();
            $actor = User::query()->whereRaw('LOWER(email) = ?', [strtolower(trim($data['actor_email']))])->first();
            if (! $actor || ! $actor->isSuperAdmin()) {
                $this->error('An existing active super administrator is required.');

                return self::FAILURE;
            }
        } catch (ValidationException $exception) {
            $this->error(implode(' ', $exception->validator->errors()->all()));

            return self::FAILURE;
        }
        if (! $this->confirm('Create '.$data['email'].' as admin (website publishing and guest inbox; no user management), approved by '.$actor->email.', with private password setup?')) {
            return self::FAILURE;
        }
        $path = null;
        $written = false;
        try {
            $directory = storage_path('app/private/account-setup-handoff');
            if (is_link($directory) || (! is_dir($directory) && ! @mkdir($directory, 0700, true)) || ! chmod($directory, 0700)) {
                throw new RuntimeException('Owner-only private handoff storage is unavailable.');
            }
            $path = $directory.'/'.$data['filename'];
            $target = $users->prepareAccountSetup($actor, $data, function (#[SensitiveParameter] string $url) use ($path, &$written): void {
                $oldUmask = umask(0077);
                try {
                    $file = @fopen($path, 'x');
                } finally {
                    umask($oldUmask);
                }
                if ($file === false) {
                    throw new RuntimeException('Cannot exclusively create the handoff file.');
                }
                $written = true;
                try {
                    if (! chmod($path, 0600) || fwrite($file, $url) !== strlen($url) || ! fflush($file)) {
                        throw new RuntimeException('Cannot securely write the handoff file.');
                    }
                } finally {
                    fclose($file);
                }
            });
        } catch (Throwable $exception) {
            if ($written && $path !== null) {
                @unlink($path);
            }
            if ($exception instanceof ValidationException) {
                $this->error(implode(' ', $exception->validator->errors()->all()));
            } elseif ($exception instanceof AuthorizationException) {
                $this->error('Super administrator authorization failed. No account created.');
            } else {
                $this->error('Private setup failed and database changes were rolled back. No URL displayed.');
            }

            return self::FAILURE;
        }
        $createdAt = DB::table(config('auth.passwords.users.table'))->where('email', $target->email)->value('created_at');
        $this->info('Administrator created with no password or email. Authenticator MFA remains mandatory.');
        $this->info('Private setup file: '.$path);
        $this->info('One-use setup expires at '.Carbon::parse($createdAt)->addMinutes(60)->utc()->format('Y-m-d H:i:s').' UTC.');

        return self::SUCCESS;
    }
}
