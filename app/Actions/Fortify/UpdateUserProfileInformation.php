<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'sexe' => ['required'],
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'tel1' => ['nullable', 'max:16'],
            'tel2' => ['nullable', 'max:16'],
            'username' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // email synced to username thanks to mutator setUsernameAttribute in App\Models\User
            'photo' => ['nullable', 'image', 'max:1024'],
            'iban' => ['nullable', 'iban'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['username'] !== $user->username &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'sexe' => $input['sexe'],
                'prenom' => $input['prenom'],
                'nom' => $input['nom'],
                'tel1' => $input['tel1'],
                'tel2' => $input['tel2'],
                'name' => $input['prenom'].' '.$input['nom'],
                'username' => $input['username'],
                'iban' => $input['iban'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'sexe' => $input['sexe'],
            'prenom' => $input['prenom'],
            'nom' => $input['nom'],
            'name' => $input['prenom'].' '.$input['nom'],
            /* 'email' => $input['email'], */
            'tel1' => $input['tel1'],
            'tel2' => $input['tel2'],
            'iban' => $input['iban'],
            'username' => $input['username'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
