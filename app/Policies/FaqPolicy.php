<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageWebsite();
    }

    public function view(User $user, Faq $record): bool
    {
        return $user->canManageWebsite();
    }

    public function create(User $user): bool
    {
        return $user->canManageWebsite();
    }

    public function update(User $user, Faq $record): bool
    {
        return $user->canManageWebsite();
    }

    public function delete(User $user, Faq $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
