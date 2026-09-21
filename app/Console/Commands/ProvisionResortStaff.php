<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProvisionResortStaff extends Command
{
    protected $signature = 'website:staff {email} {--name=} {--revoke : Remove staff access}';

    protected $description = 'Deliberately grant or revoke resort administrator access; new accounts require an interactive password';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        if (Validator::make(['email' => $email], ['email' => 'required|email|max:254'])->fails()) {
            $this->error('Enter a valid email address.');

            return self::FAILURE;
        }
        $user = User::query()->where('email', $email)->first();
        if ($this->option('revoke')) {
            if (! $user) {
                $this->error('Account not found.');

                return self::FAILURE;
            }
            $user->forceFill(['is_staff' => false])->save();
            $this->info('Staff access removed.');

            return self::SUCCESS;
        }
        if (! $user) {
            if (! $this->input->isInteractive()) {
                $this->error('Creating staff requires an interactive password prompt.');

                return self::FAILURE;
            }
            $name = $this->option('name') ?: $this->ask('Staff name');
            $password = $this->secret('Password (at least 12 characters, mixed case, letters and numbers)');
            $validator = Validator::make(['name' => $name, 'password' => $password], ['name' => 'required|string|max:255', 'password' => ['required', Password::min(12)->mixedCase()->numbers()]]);
            if ($validator->fails()) {
                $this->error(implode(' ', $validator->errors()->all()));

                return self::FAILURE;
            }
            $user = new User(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);
        }
        $user->forceFill(['is_staff' => true])->save();
        $this->info('Staff access granted. Enrol in authenticator MFA at /admin before using the panel.');

        return self::SUCCESS;
    }
}
