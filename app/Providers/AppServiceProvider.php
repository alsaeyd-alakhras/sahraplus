<?php

namespace App\Providers;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Admin;
use App\Models\Short;
use App\Models\Episod;
use App\Models\Person;
use App\Models\Season;
use App\Models\TmdbSyncLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Observers\UserObserver;
use App\Observers\AdminObserver;
use App\Observers\ShortObserver;
use App\Observers\PersonObserver;
use App\Observers\SeasonObserver;
use App\Observers\EpisodeObserver;
use App\Observers\ConstantObserver;
use App\Observers\CurrencyObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Observers\TmdbSyncLogObserver;
use App\Observers\NotificationObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Currency;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('abilities', function () {
            return include base_path('data/abilities.php');
        });
        $this->app->bind('constants', function () {
            return include base_path('data/constants.php');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Paginator::useBootstrapFive();

        RateLimiter::for('api', function (Request $request) {
            return [
                // 60 طلب بالدقيقة، ويحسب حسب المستخدم إن وُجد، وإلا حسب IP
                Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        //Authouration
        Gate::before(function ($user, $ability) {
            if ($user instanceof Admin) {
                if ($user->super_admin) {
                    return true;
                }
            }
        });
        // the Authorization for Report Page
        Gate::define('report.view', function ($user) {
            if ($user instanceof Admin) {
                if ($user->roles->contains('role_name', 'report.view')) {
                    return true;
                }
                return false;
            }
        });



        // Observe For Models
        User::observe(UserObserver::class);
        Admin::observe(AdminObserver::class);
        // Constant::observe(ConstantObserver::class);
        // Currency::observe(CurrencyObserver::class);
        Episod::observe(EpisodeObserver::class);
        Notification::observe(NotificationObserver::class);
        Person::observe(PersonObserver::class);
        Season::observe(SeasonObserver::class);
        Short::observe(ShortObserver::class);
        TmdbSyncLog::observe(TmdbSyncLogObserver::class);

        View::composer('*', function ($view) {
            $view->with([
                'auth_admin' => Auth::guard('admin')->check() ? Auth::guard('admin')->user() : null,
                'auth_user' => Auth::guard('web')->check() ? User::find(Auth::guard('web')->user()?->id)->with('profiles', 'sessions')->first() : null,
                'settings' => SystemSetting::get()->pluck('value', 'key')->toArray(),
            ]);
        });
    }
}
