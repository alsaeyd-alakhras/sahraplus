<?php

use App\Http\Middleware\LogLastUserActivity;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ✅ 1) سجّل aliases لميدلويرات الترجمة
        $middleware->alias([
            'localize'              => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'  => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'  => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'        => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
        ]);

        // (اختياري) لو حابب تضيف ميدلوير عام للويب/الـ API خليه هون:
        $middleware->web([
            LogLastUserActivity::class,
            // Alkoumi\LaravelArabicNumbers\Http\Middleware\ConvertArabicDigitsToEnlishMiddleware::class
        ]);

        $middleware->api([
            LogLastUserActivity::class,
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // معالجة أخطاء التحقق (Validation) في API routes
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('validation.validation_failed'),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // معالجة الأخطاء العامة في API routes (يتم تطبيقه فقط إذا لم يتم معالجة الاستثناء مسبقاً)
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') && !($e instanceof \Illuminate\Validation\ValidationException)) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                $message = $e->getMessage() ?: __('controller.Something_went_wrong');
                
                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                ], $status);
            }
        });
    })->create();
