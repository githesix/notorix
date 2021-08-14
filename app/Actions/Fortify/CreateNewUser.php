<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'sexe' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'tel1' => ['nullable', 'max:16'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();
        
        $calculs = new \App\Maison\Calculs();

        return User::create([
            'sexe' => $input['sexe'],
            'prenom' => $input['prenom'],
            'nom' => $input['nom'],
            'email' => $input['email'],
            'username' => $input['email'],
            'name' => $input['prenom'].' '.$input['nom'],
            'tel1' => $input['tel1'],
            'password' => Hash::make($input['password']),
            'secu' => $calculs->tokenize($input['password']),
            'ou' => env('OU', 'thesix'),
            'statut' => 0, // E-mail à confirmer (obsolète avec Laravel 7)
            'role' => 1, // Visiteur
            'solde' => 0,
            'uid' => 'u'.env('FASE_ECOLE').\App\Maison\UUID::uid8(),
            'password' => Hash::make($input['password']),
        ]);
    }
}
