<?php

namespace App\Policies;

use App\Models\ResortProfile;
use App\Models\User;

class ResortProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageWebsite();
    }

    public function view(User $user, ResortProfile $record): bool
    {
        return $user->canManageWebsite();
    }

    public function create(User $user): bool
    {
        return $user->canManageWebsite() && ! ResortProfile::query()->exists();
    }

    public function update(User $user, ResortProfile $record): bool
    {
        return $user->canManageWebsite();
    }

    public function delete(User $user, ResortProfile $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
