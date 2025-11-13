<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ViewingHistory;
use Illuminate\Auth\Access\Response;

class ViewingHistoryPolicy
{
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
    public function view(User $user, ViewingHistory $viewingHistory): bool
    {
        return $this->ownsProfile($user, $favorite->profile_id);
    }


    public function update(User $user, ViewingHistory $viewingHistory): bool
    {
        return $this->ownsProfile($user, $favorite->profile_id);
    }


    public function delete(User $user, ViewingHistory $viewingHistory): bool
    {
        return $this->ownsProfile($user, $favorite->profile_id);
    }

}