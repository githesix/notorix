<?php

namespace App\Actions\Jetstream;

use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function delete($user)
    {
        $hasard = substr(str_shuffle(MD5(microtime())), 0, 4).'_';
        $user->email = $hasard.$user->email;
        $user->username = $hasard.$user->username;
        $user->save();
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();
    }
}
