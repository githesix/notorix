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
        $hasard = '_'.substr(str_shuffle(MD5(microtime())), 0, 6).'_';
        $user->email = $hasard.$user->email;
        $user->username = $hasard.$user->username;
        $user->save();
    }
}
