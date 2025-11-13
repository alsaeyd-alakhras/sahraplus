<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Favorite;
use App\Policies\Traits\OwnsProfileTrait;

class FavoritePolicy
{
    use OwnsProfileTrait;

    // المستخدم يقدر يشوف فقط السجلات اللي تخص أحد بروفايلاته
    public function viewAny(User $user)
    {
        return true;
    }

    // المستخدم يقدر ينشئ فقط في بروفايل يخصه
    public function create(User $user, int $profileId)
    {
        return $this->ownsProfile($user, $profileId);
    }
    public function view(User $user, Favorite $favorite)
    {
        return $this->ownsProfile($user, $favorite->profile_id);
    }

    public function update(User $user, Favorite $favorite)
    {
        return $this->ownsProfile($user, $favorite->profile_id);
    }

    public function delete(User $user, Favorite $favorite)
    {
        return $this->ownsProfile($user, $favorite->profile_id);
    }
}
