<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\UserDeleted;
use App\Listeners\DeletedUser;
use App\Events\UserUpdated;
use App\Listeners\SamlAssertionAttributes;
use App\Listeners\UpdatedUser;
use App\Listeners\UserEventSubscriber;
use CodeGreenCreative\SamlIdp\Events\Assertion;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserDeleted::class => [
            DeletedUser::class,
        ],
        UserUpdated::class => [
            UpdatedUser::class,
        ],
        Assertion::class => [
            SamlAssertionAttributes::class, // Adds FirstName, LastName, Role... to SAML attributes
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserEventSubscriber::class,
    ];
}
