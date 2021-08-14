<?php

namespace App\Listeners;

use App\Events\PasswordUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserPassword
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PasswordUpdated  $event
     * @return void
     */
    public function handle(PasswordUpdated $event)
    {
        //
    }
}
