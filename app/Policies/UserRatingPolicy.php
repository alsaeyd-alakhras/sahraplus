<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRating;
use Illuminate\Auth\Access\Response;
use App\Traits\OwnsProfileTrait;

class UserRatingPolicy
{
    use OwnsProfileTrait;

    public function viewAny(User $user)
    {
        return true;
    }

    public function create(User $user, int $profileId)
    {
        return $this->ownsProfile($user, $profileId);
    }
    public function view(User $user, UserRating $userRating): bool
    {
        return $this->ownsProfile($user, $userRating->profile_id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserRating $userRating): bool
    {
        return $this->ownsProfile($user, $userRating->profile_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserRating $userRating): bool
    {
        return $this->ownsProfile($user, $userRating->profile_id);
    }


}
