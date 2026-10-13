<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, User $record): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, User $record): bool
    {
        return $user->isSuperAdmin() && ! $user->is($record);
    }

    public function sendSetupLink(User $user, User $record): bool
    {
        return $this->update($user, $record) && $record->canManageGuestRequests();
    }

    public function generateSetupLink(User $user, User $record): bool
    {
        return $this->sendSetupLink($user, $record) && $record->password === null
            && filled($user->getAppAuthenticationSecret());
    }

    public function delete(User $user, User $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
