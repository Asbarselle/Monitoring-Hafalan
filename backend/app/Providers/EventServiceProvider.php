<?php

namespace App\Providers;

use App\Events\HafalanCreated;
use App\Listeners\SendHafalanNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        HafalanCreated::class => [
            SendHafalanNotification::class,
        ],
    ];

    /**
     * Enable the application events.
     */
    public function boot(): void
    {
        //
    }
}
