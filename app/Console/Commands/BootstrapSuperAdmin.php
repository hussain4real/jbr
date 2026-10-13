<?php

namespace App\Console\Commands;

use App\Actions\ManageResortUsers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BootstrapSuperAdmin extends Command
{
    protected $signature = 'website:super-admin {email : Exact approved existing account email}';

    protected $description = 'Interactively bootstrap the first super administrator; never creates an account or changes credentials';

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
        if (! $this->confirm('Grant '.$email.' full resort access, including user and role management?')) {
            return self::FAILURE;
        }
        try {
            $users->bootstrap($email);
        } catch (ValidationException $exception) {
            $this->error(implode(' ', $exception->validator->errors()->all()));

            return self::FAILURE;
        }
        $this->info('Super administrator access granted. Existing password and authenticator MFA are preserved.');

        return self::SUCCESS;
    }
}
