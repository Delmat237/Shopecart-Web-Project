<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Listeners\NotifyAdminNewOrder;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\UpdateOrderStatistics;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Événement OrderCreated avec ses 3 listeners
        OrderCreated::class => [
            SendOrderConfirmationEmail::class,
            NotifyAdminNewOrder::class,
            UpdateOrderStatistics::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
