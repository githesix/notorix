<?php

namespace App\Listeners;

use App\Models\Account;
use App\Models\Alias;
use App\Maison\Calculs;

/*
 * User access events listeners
 * Subscribed in App\Providers\EventServiceProvider $subscribe
 */
class UserEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function onUserLogin($event) {
        $email = $event->user->email;
        $name = $event->user->name;
        info(__('Login of :user', ['user' => "$name ($email)"]));
    }

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event) {
        $email = $event->user->email;
        $name = $event->user->name;
        info(__('Logout of :user', ['user' => "$name ($email)"]));
    }
    /**
     * Handle password reset events.
     * (moved to Notorix-exim package)
     * kept here for the archives
     */
    /*public function onPasswordReset($event) {
        $email = $event->user->email;
        $name = $event->user->name;
        // Compte e-mail associé?
        if ($event->user->exim['username']) {
            $u = $event->user;
            $calculs = new Calculs();
            if ($u->exim['type'] == 'account') {
                $account = Account::find($u->exim['username']);
                $pw = $calculs->detokenize($u->secu);
                $account->password = hash('sha256', $pw);
                $account->save();
            }
        }
        $msg = isset($account) ? " et mise à jour du mail associé" : "";
        info("Changement du mot de passe de $name ($email)". $msg);
    }*/

    // Met le statut à 1 et synchronise l'alias avec le nouvel email
    /**
     * Handle e-mail verification after registration and updates
     * Set statut to 1
     * Alias synchronization has moved to Notorix-exim package
     */
    public function onVerified($event) {
        $user = $event->user;
        $user->setFlag(1, 1);
        /*if ($user->exim['type'] == 'alias') {
            $alias = Alias::where('address', $user->exim['username'])->first();
            $alias->goto = $user->email;
            $alias->save();
        }*/
        $user->save();
        info(__('Address :email confirmed for :user', ['email' => $user->email, 'user' => $user->name]));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Listeners\UserEventSubscriber@onUserLogin'
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'App\Listeners\UserEventSubscriber@onUserLogout'
        );

        /*$events->listen(
            'Illuminate\Auth\Events\PasswordReset',
            'App\Listeners\UserEventSubscriber@onPasswordReset'
        );*/
        // E-mail vérifié
        $events->listen(
            'Illuminate\Auth\Events\Verified',
            'App\Listeners\UserEventSubscriber@onVerified'
        );
    }
    /*
     * En plus de Login et Logout, Illuminate\Auth\Events\* propose:
     * Registered - Attempting - Authenticated - Failed - Lockout - PasswordReset
     */

}
