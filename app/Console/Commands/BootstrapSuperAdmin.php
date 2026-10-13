<?php

namespace App\Console\Commands;

use App\Actions\ManageResortUsers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use SensitiveParameter;
use Throwable;

class BootstrapSuperAdmin extends Command
{
    protected $signature = 'website:super-admin {email : Exact approved account email}
        {--create : Create the first owner without a password}
        {--name= : Name of the new owner}
        {--setup-file= : Exclusive private handoff filename ending in .link; never prints or emails the link}';

    protected $description = 'Interactively bootstrap the first super administrator or prepare passwordless first-owner setup';

    public function handle(ManageResortUsers $users): int
    {
        $email = (string) $this->argument('email');
        if (Validator::make(['email' => $email], ['email' => 'required|email|max:254'])->fails()) {
            $this->error('Enter a valid account email address.');

            return self::FAILURE;
        }
        if (! $this->input->isInteractive()) {
            $this->error('Super administrator bootstrap requires interactive confirmation.');

            return self::FAILURE;
        }
        $filename = $this->option('setup-file');
        $create = (bool) $this->option('create');
        if (($create && ! is_string($filename)) || (is_string($filename) && ! preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_-]{0,63}\.link\z/', $filename))) {
            $this->error('Passwordless creation requires a private handoff filename such as owner-setup.link.');

            return self::FAILURE;
        }
        if ($this->option('name') && ! $create) {
            $this->error('The name option is only valid when creating the first owner.');

            return self::FAILURE;
        }
        if (! $this->confirm('Grant '.$email.' full resort access, including user and role management?')) {
            return self::FAILURE;
        }
        $path = null;
        $written = false;
        try {
            if (is_string($filename)) {
                $path = $this->handoffPath($filename);
                $users->prepareOwnerSetup($email, $this->option('name'), $create, function (#[SensitiveParameter] string $url) use ($path, &$written): void {
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
            } else {
                $users->bootstrap($email);
            }
        } catch (ValidationException $exception) {
            $this->error(implode(' ', $exception->validator->errors()->all()));

            return self::FAILURE;
        } catch (Throwable) {
            if ($written && $path !== null) {
                @unlink($path);
            }
            $this->error('Setup failed and database changes were rolled back. Check private handoff storage without displaying its contents.');

            return self::FAILURE;
        }
        if ($path !== null) {
            $this->info('Password setup prepared in private storage: '.$path);
            $this->info('No password generated and no email sent. Link expiry follows the users password broker; MFA remains required.');

            return self::SUCCESS;
        }
        $this->info('Super administrator access granted. Existing password and authenticator MFA are preserved.');

        return self::SUCCESS;
    }

    private function handoffPath(string $filename): string
    {
        $directory = storage_path('app/private/owner-setup-handoff');
        if (is_link($directory)) {
            throw new RuntimeException('Private handoff storage cannot be a symbolic link.');
        }
        if (! is_dir($directory) && ! @mkdir($directory, 0700, true)) {
            throw new RuntimeException('Private handoff storage is unavailable.');
        }
        if (! chmod($directory, 0700)) {
            throw new RuntimeException('Private handoff storage permissions are unavailable.');
        }

        return $directory.'/'.$filename;
    }
}
