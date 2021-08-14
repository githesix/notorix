<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeletedUser
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
     * @param  UserDeleted  $event
     * @return void
     */
    public function handle(UserDeleted $event)
    {
        $user = $event->user;
        $name = auth()->user()->name ?? 'Unauthenticated user';
        info(__(":admin deleted user :user", [
            'admin' => auth()->user()->name ?? 'Unauthenticated user',
             'user' => "{$user->name} ({$user->email})"]));
        $email = '_'.rand().'_'.$user->email;
        $user->email = $email;
        $user->username = $email;
        $user->save();
    }
}
