<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Mail\InvitationPreinscription;
use App\Maison\Calculs;
use App\Maison\UUID;
use App\Models\Groupe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;

class IndexController extends Controller
{
    use PasswordValidationRules;

    /**
     * UNUSED
     * @TODO remove in next version
     */
    public function charte() {
        $charte = \config('perso.charte_util');
        return response()->download($charte);
    }
    
    /**
     * preregister API call
     *
     * @param  mixed $request array (
     *   'tokenix' => 'UID of registrating authorized user'
     *   'preuser' =>
     *   array (
     *     'sexe' => 'F',
     *     'prenom' => 'Aline',
     *     'nom' => 'Laboury',
     *     'email' => 'aline.laboury@nomail.dev',
     *     'role' => null,
     *     'groupe' => null,
     *     'groupe_description' => null,
     *   ),
     *   'bleuid' => null,
     * )
     * @return void
     * Test with curl: 
     * curl -i -X POST -H 'Content-Type: application/json' -d '{"tokenix": "ade95e3c-8698-3aa2-b7ff-cc122874a8ad","preuser": {"sexe": "m", "prenom": "Jacques", "nom": "Dutronc", "email": "cactus@example.net"}}' http://notorix/api/preregister
     */
    public function preregister(Request $request)
    {
        $preuser = $request->input('preuser');
        $tokenix = $request->input('tokenix') ?? null;
        $apiuser = $tokenix ? User::findByUid($tokenix) : null;
        if ($apiuser && $preuser) {
            $ret = User::preRegister($preuser);
            $prenom = $preuser['prenom'];
            $nom = $preuser['nom'];
            $email = $preuser['email'];
            info(__(':user pre-register and invite :preuser', ['user' => $apiuser->name, 'preuser' => "$prenom $nom ($email)"]));
            return $ret;
        } else {
            return ["error" => __("Unauthorized request")];
        }
    }

    public function get_confirm_preregistration(Request $request, $token)
    {
        $user = User::findByUid($token);
        if (!$user) {
            return redirect()->route('dashboard');
        }
        if (!is_null($user->statut)) {
            info(__('Preregister link clicked for :user', ['user' => $user->name]));
            return redirect()->route('dashboard');
        }
        return view('auth.confirm_preregistration', ['user' => $user, 'request' => $request]);
    }

    public function post_confirm_preregistration(Request $request)
    {
        $user = User::findByUid($request->input('token'));
        Validator::make($request->input(), [
            'password' => $this->passwordRules(),
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();
        $calculs = new Calculs();
        $secu = $calculs->tokenize($request->input('password'));
        $user->password = Hash::make($request->input('password'));
        $user->secu = $secu;
        $user->statut = 1;
        if($request->input('email') == $user->email) {
            $user->email_verified_at = Carbon::now();
        } else {
            $user->email = $request->input('email');
            $user->username = $request->input('email');
        }
        $user->save();
        return redirect()->route('dashboard');
    }
}
