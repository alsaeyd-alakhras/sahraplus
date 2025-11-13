<?php

namespace App\Traits;

use App\Models\User;

trait OwnsProfileTrait
{
    protected function ownsProfile(User $user, int $profileId): bool
    {
        return $user->profiles()->whereKey($profileId)->exists();
    }
}
