<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'pin_code',
        'date_of_birth',
        'gender',
        'country_code',
        'language',
        'avatar_url',
        'is_active',
        'is_banned',
        'email_notifications',
        'push_notifications',
        'parental_controls',
        'last_activity',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'last_activity' => 'datetime',
        'is_active' => 'boolean',
        'is_banned' => 'boolean',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'parental_controls' => 'boolean',
    ];

    protected $appends = ['avatar_full_url', 'full_name'];

    // العلاقات
    public function profiles()
    {
        return $this->hasMany(UserProfile::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notifiable_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    public function watchlist()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function watchProgress()
    {
        return $this->hasMany(WatchProgres::class);
    }

    public function viewingHistory()
    {
        return $this->hasMany(ViewingHistory::class);
    }

    public function ratings()
    {
        return $this->hasMany(UserRating::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    // Accessors & Mutators
    public function getFullNameAttribute() // $user->full_name
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getAvatarFullUrlAttribute() // $user->avatar_full_url
    {
        if ($this->avatar_url) {
            return asset('storage/'.$this->avatar_url);
        }

        return asset('assets/img/avatars/1.png');
    }

    public function verifyPin($pin)
    {
        return $this->pin_code === $pin;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotBanned($query)
    {
        return $query->where('is_banned', false);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where(function ($q) {
                $q->where('status', 'trial')
                    ->where('trial_ends_at', '>=', now())
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'active')
                            ->where('starts_at', '<=', now())
                            ->where('ends_at', '>=', now());
                    });
            })
            ->latest('ends_at');
    }

    public function latestSubscription()
    {
        return $this->hasOne(UserSubscription::class)->latestOfMany();
    }

    public function currentPlan()
    {
        return $this->hasOneThrough(
            SubscriptionPlan::class,
            UserSubscription::class,
            'user_id',      // foreign key on user_subscriptions table
            'id',           // primary key of subscription_plans
            'id',           // primary key of users
            'plan_id'       // foreign key on user_subscriptions table
        )
           // ->whereIn('user_subscriptions.status', ['active', 'trial'])
            ->where('user_subscriptions.starts_at', '<=', now())
            ->where('user_subscriptions.ends_at', '>=', now());
    }

    public function currentContentAccess()
    {
        $plan = $this->currentPlan()->with('contentAccess')->first();

        return $plan ? $plan->contentAccess : collect([]);
    }
}
