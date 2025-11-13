<?php

namespace App\Policies;

use App\Models\Download;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Policies\Traits\OwnsProfileTrait;


class DownloadPolicy
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
    public function view(User $user, Download $download): bool
    {
        return $this->ownsProfile($user, $download->profile_id);
    }

    public function update(User $user, Download $download): bool
    {
        return $this->ownsProfile($user, $download->profile_id);
    }

    public function delete(User $user, Download $download): bool
    {
        return $this->ownsProfile($user, $download->profile_id);
    }


}