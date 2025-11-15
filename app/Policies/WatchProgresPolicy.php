<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WatchProgres;
use Illuminate\Auth\Access\Response;
use App\Traits\OwnsProfileTrait;


class WatchProgresPolicy
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
    public function view(User $user, WatchProgres $watchProgress): bool
    {
        return $this->ownsProfile($user, $watchProgress->profile_id);
    }

    public function update(User $user, WatchProgres $watchProgress): bool
    {
        return $this->ownsProfile($user, $watchProgress->profile_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WatchProgres $watchProgress): bool
    {
        return $this->ownsProfile($user, $watchProgress->profile_id);
    }


}
