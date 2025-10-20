<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\FailedLoginListener',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\TrackUserLoginSession',
        ],
		'Illuminate\Auth\Events\Logout' => [
			'App\Listeners\TrackUserLogoutSession',
		]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}