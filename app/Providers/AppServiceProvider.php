<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Delivery;
use App\Observers\DeliveryObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Laravel\Reverb\Events\MessageReceived;
use App\Listeners\CaptureMobileLocationTrigger;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register HIPAA audit observers
        if (config('hipaa.enabled', true)) {
            Delivery::observe(DeliveryObserver::class);
        }

        Gate::define('view-enquiries', function ($user) {
            return $user->role_id == 1; // Only allow users with role_id 1 (super admin) to view enquiries   
        });

        Event::listen(
            MessageReceived::class,
            CaptureMobileLocationTrigger::class
        );

    }
}
