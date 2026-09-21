<?php

namespace App\Policies;

use App\Models\GuestRequest;
use App\Models\User;

class GuestRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_staff === true;
    }

    public function view(User $user, GuestRequest $record): bool
    {
        return $user->is_staff === true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, GuestRequest $record): bool
    {
        return $user->is_staff === true;
    }

    public function delete(User $user, GuestRequest $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
