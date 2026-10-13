<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Carbon;
use SensitiveParameter;

class SetupTokenRepository extends DatabaseTokenRepository
{
    public function create(CanResetPassword $user): string
    {
        return $this->connection->transaction(function () use ($user): string {
            User::query()->where('email', $user->getEmailForPasswordReset())->lockForUpdate()->first();

            return parent::create($user);
        }, 3);
    }

    public function exists(CanResetPassword $user, #[SensitiveParameter] $token): bool
    {
        $record = $this->getTable()->where('email', $user->getEmailForPasswordReset())->first();
        if ($record?->setup_expires_at === null) {
            return parent::exists($user, $token);
        }

        return $user instanceof User && $user->canManageGuestRequests() && $user->password === null
            && Carbon::parse($record->setup_expires_at)->isFuture()
            && $this->hasher->check($token, $record->token);
    }

    public function deleteExpired(): void
    {
        $this->getTable()->where(function ($query): void {
            $query->whereNotNull('setup_expires_at')->where('setup_expires_at', '<=', Carbon::now());
        })->orWhere(function ($query): void {
            $query->whereNull('setup_expires_at')->where('created_at', '<', Carbon::now()->subSeconds($this->expires));
        })->delete();
    }
}
