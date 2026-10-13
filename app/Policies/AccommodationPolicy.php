<?php

namespace App\Policies;

use App\Models\Accommodation;
use App\Models\User;

class AccommodationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageWebsite();
    }

    public function view(User $user, Accommodation $record): bool
    {
        return $user->canManageWebsite();
    }

    public function create(User $user): bool
    {
        return $user->canManageWebsite();
    }

    public function update(User $user, Accommodation $record): bool
    {
        return $user->canManageWebsite();
    }

    public function delete(User $user, Accommodation $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
