<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Auth\Access\HandlesAuthorization;

class WatchlistPolicy
{
    use HandlesAuthorization;

    /**
     * تحقق إذا يمكن للمستخدم عرض أي Watchlists لبروفايل معين
     */
    public function viewAny(User $user, $profileId)
    {
        return $user->profiles()->where('id', $profileId)->exists();
    }

    /**
     * تحقق إذا يمكن للمستخدم عرض Watchlist محدد
     */
    public function view(User $user, Watchlist $watchlist)
    {
        return $watchlist->user_id === $user->id
            && $user->profiles()->where('id', $watchlist->profile_id)->exists();
    }

    /**
     * تحقق إذا يمكن للمستخدم إنشاء Watchlist لبروفايل معين
     */
    public function create(User $user, $profileId)
    {
        return $user->profiles()->where('id', $profileId)->exists();
    }

    /**
     * تحقق إذا يمكن للمستخدم حذف Watchlist
     */
    public function delete(User $user, Watchlist $watchlist)
    {
        return $watchlist->user_id === $user->id
            && $user->profiles()->where('id', $watchlist->profile_id)->exists();
    }
}