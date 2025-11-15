<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Auth\Access\Response;
use App\Traits\OwnsProfileTrait;

class WatchlistPolicy
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
    public function view(User $user, Watchlist $watchlist): bool
    {
        return $this->ownsProfile($user, $watchlist->profile_id);
    }


    public function update(User $user, Watchlist $watchlist): bool
    {
        return $this->ownsProfile($user, $watchlist->profile_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Watchlist $watchlist): bool
    {
        return $this->ownsProfile($user, $watchlist->profile_id);
    }


}