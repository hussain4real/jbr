<?php

namespace App\Actions;

use App\Models\User;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class ManageResortUsers
{
    /** @param array<string, mixed> $data */
    public function create(User $actor, array $data): User
    {
        return DB::transaction(function () use ($actor, $data): User {
            $users = $this->lockedUsers();
            Gate::forUser($users->find($actor->id))->authorize('create', User::class);
            $data['email'] = Str::lower(trim((string) ($data['email'] ?? '')));
            $data = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:254', Rule::unique('users', 'email')],
                'resort_role' => ['required', Rule::in(array_keys(User::resortRoles()))],
            ])->validate();
            if (User::query()->whereRaw('LOWER(email) = ?', [$data['email']])->exists()) {
                throw ValidationException::withMessages(['email' => 'An account with this email address already exists.']);
            }
            $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => Str::random(64)]);
            $user->forceFill(['resort_role' => $data['resort_role'], 'is_staff' => true])->save();
            $this->audit('created', $actor, $user);

            return $user;
        }, 3);
    }

    /** @param array<string, mixed> $data */
    public function update(User $actor, User $target, array $data): User
    {
        return DB::transaction(function () use ($actor, $target, $data): User {
            $users = $this->lockedUsers();
            $target = $users->find($target->id) ?? abort(404);
            Gate::forUser($users->find($actor->id))->authorize('update', $target);
            $data = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'resort_role' => ['required', Rule::in(array_keys(User::resortRoles()))],
                'is_staff' => ['required', 'boolean'],
            ])->validate();
            if ($target->isSuperAdmin() && (! $data['is_staff'] || $data['resort_role'] !== User::ROLE_SUPER_ADMIN)
                && $users->filter(fn (User $user): bool => $user->isSuperAdmin())->count() <= 1) {
                throw ValidationException::withMessages(['resort_role' => 'The last active super administrator cannot be demoted or disabled.']);
            }
            $target->forceFill($data)->save();
            $this->audit('updated', $actor, $target);

            return $target;
        }, 3);
    }

    public function sendSetupLink(User $actor, User $target): void
    {
        DB::transaction(function () use ($actor, $target): void {
            $users = $this->lockedUsers();
            $target = $users->find($target->id) ?? abort(404);
            Gate::forUser($users->find($actor->id))->authorize('sendSetupLink', $target);
            $status = Password::broker('users')->sendResetLink(['email' => $target->email], function (User $user, #[SensitiveParameter] string $token): void {
                $notification = new ResetPassword($token);
                $notification->url = Filament::getPanel('admin')->getResetPasswordUrl($token, $user);
                $notification->afterCommit();
                $user->notify($notification);
            });
            if ($status !== Password::RESET_LINK_SENT) {
                throw ValidationException::withMessages(['email' => __($status)]);
            }
            $this->audit('setup_link_requested', $actor, $target);
        }, 3);
    }

    public function bootstrap(string $email): User
    {
        return DB::transaction(function () use ($email): User {
            $users = $this->lockedUsers();
            if ($users->contains(fn (User $user): bool => $user->resort_role === User::ROLE_SUPER_ADMIN)) {
                throw ValidationException::withMessages(['email' => 'A super administrator already exists. Use user management for further access changes.']);
            }
            $target = $users->first(fn (User $user): bool => Str::lower($user->email) === Str::lower(trim($email)));
            if (! $target) {
                throw ValidationException::withMessages(['email' => 'Account not found. Provision the approved account before bootstrapping.']);
            }
            $target->forceFill(['resort_role' => User::ROLE_SUPER_ADMIN, 'is_staff' => true])->save();
            Log::notice('Resort super administrator bootstrapped by server operator', ['target_id' => $target->id]);

            return $target;
        }, 3);
    }

    /** @return Collection<int, User> */
    private function lockedUsers(): Collection
    {
        return User::query()->orderBy('id')->lockForUpdate()->get();
    }

    private function audit(string $event, User $actor, User $target): void
    {
        Log::notice('Resort user access '.$event, ['actor_id' => $actor->id, 'target_id' => $target->id, 'role' => $target->resortRole(), 'active' => $target->is_staff]);
    }
}
