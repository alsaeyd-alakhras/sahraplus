<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // if want to change guard
        $request = request();

        if ($request->is('dashboard/*')) {
            Config::set('fortify.guard', 'admin');
            Config::set('fortify.password', 'admins');
            Config::set('fortify.prefix', 'dashboard');
            Config::set('fortify.home', '/dashboard/home');
        }

        if ($request->is('/*')) {
            Config::set('fortify.guard', 'web');
            Config::set('fortify.password', 'users');
            Config::set('fortify.prefix', '');
            Config::set('fortify.home', '/home');
        }

        // if want to coustom login page
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                if (Config::get('fortify.guard') == 'admin') {
                    return redirect()->intended('/dashboard/home');
                }
                return redirect()->intended('/');
            }
        });
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                if(Config::get('fortify.guard') == 'admin'){
                    return redirect('/dashboard/login');
                }
                return redirect('/login');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(function () {
            if (Config::get('fortify.guard') == 'admin') {
                return view('auth.admin.login');
            }
            return view('auth.user.login');
        });


        Fortify::authenticateUsing(function (Request $request) {
            if (Config::get('fortify.guard') == 'admin') {
                $user = Admin::where('username', $request->username)
                            ->orWhere('email', $request->username)
                            ->first();
                if ($user && Hash::check($request->password, $user->password)) {
                    ActivityLogService::log(
                        'Login',
                        'Admin',
                        "تم تسجيل دخول",
                        null,
                        null,
                        $user->id,
                        $user->name
                    );
                    return $user;
                }
            }
            $user = User::where('email', $request->username)
                        ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                ActivityLogService::log(
                    'Login',
                    'User',
                    "تم تسجيل دخول",
                    null,
                    null,
                    $user->id,
                    $user->name
                );
                return $user;
            }
        });


        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
