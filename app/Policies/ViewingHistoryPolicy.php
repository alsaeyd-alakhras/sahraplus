<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ViewingHistory;
use Illuminate\Auth\Access\Response;
use App\Traits\OwnsProfileTrait;


class ViewingHistoryPolicy
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
    public function view(User $user, ViewingHistory $viewingHistory): bool
    {
        return $this->ownsProfile($user, $viewingHistory->profile_id);
    }


    public function update(User $user, ViewingHistory $viewingHistory): bool
    {
        return $this->ownsProfile($user, $viewingHistory->profile_id);
    }


    public function delete(User $user, ViewingHistory $viewingHistory): bool
    {
        return $this->ownsProfile($user, $viewingHistory->profile_id);
    }

}