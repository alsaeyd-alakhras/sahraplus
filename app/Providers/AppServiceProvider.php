<?php

namespace App\Providers;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Admin;
use App\Models\Short;
use App\Models\Episode;
use App\Models\Person;
use App\Models\Season;
use App\Models\TmdbSyncLog;
use App\Models\Notification;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;
use App\Observers\UserObserver;
use App\Observers\AdminObserver;
use App\Observers\ShortObserver;
use App\Observers\PersonObserver;
use App\Observers\SeasonObserver;
use App\Observers\EpisodeObserver;
use App\Observers\TmdbSyncLogObserver;
use App\Observers\NotificationObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public function register(): void
    {
        $this->app->bind('abilities', fn () => include base_path('data/abilities.php'));
        $this->app->bind('constants', fn () => include base_path('data/constants.php'));
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        RateLimiter::for('api', function (Request $request) {
            return [Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())];
        });

        Gate::before(function ($user) {
            if ($user instanceof Admin && $user->super_admin) return true;
        });

        Gate::define('report.view', function ($user) {
            if ($user instanceof Admin) {
                return $user->roles->contains('role_name', 'report.view');
            }
            return false;
        });

        // مهم: خليه يكتب 'movie' و 'short' بدل App\Models\Movie/Short
        Relation::enforceMorphMap([
            'user' => 'App\Models\User',
            'movie' => Movie::class,
            'short' => Short::class,
            'episode' => Episode::class,
            'series' => Series::class,

        ]);

        // Observers
        User::observe(UserObserver::class);
        Admin::observe(AdminObserver::class);
        Episode::observe(EpisodeObserver::class);
        Notification::observe(NotificationObserver::class);
        Person::observe(PersonObserver::class);
        Season::observe(SeasonObserver::class);
        Short::observe(ShortObserver::class);
        TmdbSyncLog::observe(TmdbSyncLogObserver::class);

        View::composer('*', function ($view) {
            $view->with([
                'auth_admin' => Auth::guard('admin')->check() ? Auth::guard('admin')->user() : null,
                'auth_user'  => Auth::guard('web')->check()
                    ? User::find(Auth::guard('web')->user()?->id)->with('profiles', 'sessions')->first()
                    : null,
                'settings'   => SystemSetting::get()->pluck('value', 'key')->toArray(),
            ]);
        });

    }
}
