<?php

namespace App\Services\Subscriptions;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserActiveDevice;
use Carbon\Carbon;

class SubscriptionAccessService
{
    /**
     * Get user's active subscription
     */
    public function getActiveSubscription(User $user): ?UserSubscription
    {
        return UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->where('ends_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Check if user can access a category
     */
    public function canAccessCategory(User $user, int $categoryId): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return false;
        }

        return $subscription->canAccessCategory($categoryId);
    }

    /**
     * Check if user can access a movie
     */
    public function canAccessMovie(User $user, int $movieId): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return false;
        }

        return $subscription->canAccessMovie($movieId);
    }

    /**
     * Check if user can access a series
     */
    public function canAccessSeries(User $user, int $seriesId): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return false;
        }

        return $subscription->canAccessSeries($seriesId);
    }

    /**
     * Check if user can use specific quality
     */
    public function canUseQuality(User $user, string $quality): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return false;
        }

        return $subscription->canUseQuality($quality);
    }

    /**
     * Check if user can download content
     */
    public function canDownload(User $user): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return false;
        }

        return $subscription->canDownload();
    }

    /**
     * Check if user can access live TV
     */
    public function canAccessLiveTV(User $user): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return false;
        }

        return $subscription->plan->live_tv_enabled;
    }

    /**
     * Check max concurrent streams
     */
    public function canStartStream(User $user, string $deviceId): array
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return [
                'allowed' => false,
                'reason' => 'no_active_subscription',
            ];
        }

        $maxStreams = $subscription->plan->max_devices;

        if (!$maxStreams) {
            return ['allowed' => true];
        }

        // Count active devices in last 5 minutes
        $activeDevicesCount = UserActiveDevice::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('last_activity', '>', Carbon::now()->subMinutes(5))
            ->where('device_id', '!=', $deviceId)
            ->count();

        if ($activeDevicesCount >= (int)$maxStreams) {
            return [
                'allowed' => false,
                'reason' => 'max_concurrent_streams_reached',
                'max_allowed' => $maxStreams,
                'current_count' => $activeDevicesCount,
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Check max devices limit
     */
    public function canRegisterDevice(User $user): array
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return [
                'allowed' => false,
                'reason' => 'no_active_subscription',
            ];
        }

        $maxDevices = $subscription->plan->max_devices;

        $registeredDevicesCount = UserActiveDevice::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();

        if ($registeredDevicesCount >= $maxDevices) {
            return [
                'allowed' => false,
                'reason' => 'max_devices_reached',
                'max_allowed' => $maxDevices,
                'current_count' => $registeredDevicesCount,
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Get access summary for user
     */
    public function getAccessSummary(User $user): array
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'plan' => null,
                'features' => [],
            ];
        }

        return [
            'has_subscription' => true,
            'plan' => [
                'name' => $subscription->plan->name_en,
                'status' => $subscription->status,
                'ends_at' => $subscription->ends_at,
            ],
            'features' => [
                'max_profiles' => $subscription->plan->max_profiles,
                'max_devices' => $subscription->plan->max_devices,
                'video_quality' => $subscription->plan->video_quality,
                'download_enabled' => $subscription->plan->download_enabled,
                'ads_enabled' => $subscription->plan->ads_enabled,
                'live_tv_enabled' => $subscription->plan->live_tv_enabled,
            ],
        ];
    }
}

