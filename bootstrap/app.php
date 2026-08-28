<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\NoCache;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/mobile')
                ->group(base_path('routes/mobile.php'));

            Broadcast::routes(['middleware' => ['auth:sanctum']]);     
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscription.feature' => \App\Http\Middleware\CheckSubscriptionFeature::class,
            'subscription.limit' => \App\Http\Middleware\CheckSubscriptionLimit::class,
            'log.phi' => \App\Http\Middleware\LogPhiAccess::class,
            'custom.auth' => \App\Http\Middleware\AuthMiddleware::class,
            'admin.auth' => \App\Http\Middleware\AdminAuthMiddleware::class,
            'hospital.auth' => \App\Http\Middleware\HospitalMiddleware::class,
        ]);

        // Apply HIPAA audit logging middleware to API routes
        $middleware->api(append: [
            \App\Http\Middleware\LogPhiAccess::class,
        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'no.cache' => NoCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
