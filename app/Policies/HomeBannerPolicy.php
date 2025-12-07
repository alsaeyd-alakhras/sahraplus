<?php

namespace App\Policies;

use App\Models\HomeBanner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HomeBannerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return true; // Admins can view
    }

    /**
     * Determine whether the user can show the model.
     */
    public function show(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Admins can create
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return true; // Admins can update
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return true; // Admins can delete
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HomeBanner $homeBanner): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HomeBanner $homeBanner): bool
    {
        return false;
    }
}

