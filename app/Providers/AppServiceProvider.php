<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use App\Observers\AdminObserver;
use App\Observers\UserObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('abilities', function() {
            return include base_path('data/abilities.php');
        });
        $this->app->bind('constants', function() {
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
            if($user instanceof Admin) {
                if($user->super_admin) {
                    return true;
                }
            }
        });
        // the Authorization for Report Page
        Gate::define('report.view', function ($user) {
            if($user instanceof Admin) {
                if($user->roles->contains('role_name', 'report.view')) {
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

        View::composer('*', function ($view) {
            $view->with([
                'auth_user' => Auth::check() ? Auth::guard('admin')->user() : null,
                'settings' => config('app.settings'),
            ]);
        });

    }
}
