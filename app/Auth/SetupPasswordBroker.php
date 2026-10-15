<?php

namespace App\Auth;

use App\Models\User;
use Closure;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\DB;
use SensitiveParameter;

class SetupPasswordBroker extends PasswordBroker
{
    /** @param array<string, mixed> $credentials */
    public function reset(#[SensitiveParameter] array $credentials, Closure $callback): string
    {
        $user = $this->getUser($credentials);
        if (! $user instanceof User) {
            return parent::reset($credentials, $callback);
        }

        return DB::transaction(function () use ($credentials, $callback, $user): string {
            User::query()->whereKey($user->id)->lockForUpdate()->first();

            return parent::reset($credentials, $callback);
        }, 3);
    }
}
