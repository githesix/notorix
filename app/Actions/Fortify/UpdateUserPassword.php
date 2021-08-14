<?php

namespace App\Actions\Fortify;

use App\Events\PasswordUpdated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($user, $input) {
            if (! Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updatePassword');

        $calculs = new \App\Maison\Calculs();
        $secu = $calculs->tokenize($input['password']);

        $user->forceFill([
            'password' => Hash::make($input['password']),
            'secu' => $secu,
        ])->save();
        $name = $user->name;
        $email = $user->email;
        info(__(':user changed password', ['user' => "$name ($email)"]));
        PasswordUpdated::dispatch($user, $input['current_password'], $input['password']);
    }
}
