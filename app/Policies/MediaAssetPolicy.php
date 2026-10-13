<?php

namespace App\Policies;

use App\Models\MediaAsset;
use App\Models\User;

class MediaAssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageWebsite();
    }

    public function view(User $user, MediaAsset $record): bool
    {
        return $user->canManageWebsite();
    }

    public function create(User $user): bool
    {
        return $user->canManageWebsite();
    }

    public function update(User $user, MediaAsset $record): bool
    {
        return $user->canManageWebsite();
    }

    public function delete(User $user, MediaAsset $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
