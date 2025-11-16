<?php

namespace App\Policies;

use App\Models\Download;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Traits\OwnsProfileTrait;


class DownloadPolicy
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
    public function view(User $user, Download $download): bool
    {
        return $this->ownsProfile($user, $download->profile_id);
    }



    public function update(User $user, Download $download): bool
    {
        if (!$download->profile_id) {
            return false;
        }
        return $this->ownsProfile($user, $download->profile_id);
    }
    public function delete(User $user, Download $download): bool
    {
        return $this->ownsProfile($user, $download->profile_id);
    }



}