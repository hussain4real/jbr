<?php

namespace App\Console\Commands;

use App\Actions\ManageResortUsers;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProvisionResortStaff extends Command
{
    protected $signature = 'website:staff {email} {--name=} {--revoke : Remove staff access}';

    protected $description = 'Deliberately grant or revoke resort staff access; new accounts require an interactive password';

    public function handle(ManageResortUsers $users): int
    {
        $email = (string) $this->argument('email');
        if (Validator::make(['email' => $email], ['email' => 'required|email|max:254'])->fails()) {
            $this->error('Enter a valid email address.');

            return self::FAILURE;
        }
        $user = User::query()->whereRaw('LOWER(email) = ?', [strtolower(trim($email))])->first();
        $revoke = (bool) $this->option('revoke');
        $name = null;
        $password = null;
        if (! $user && ! $revoke) {
            if (! $this->input->isInteractive()) {
                $this->error('Creating staff requires an interactive password prompt.');

                return self::FAILURE;
            }
            $name = $this->option('name') ?: $this->ask('Staff name');
            $password = $this->secret('Password (at least 12 characters, mixed case, letters and numbers)');
        }
        try {
            $users->provisionStaff($email, $revoke, $name, $password);
        } catch (ValidationException $exception) {
            $this->error(implode(' ', $exception->validator->errors()->all()));

            return self::FAILURE;
        }
        $this->info($revoke ? 'Staff access removed.' : 'Staff access granted. Enrol in authenticator MFA at /admin before using the panel.');

        return self::SUCCESS;
    }
}
