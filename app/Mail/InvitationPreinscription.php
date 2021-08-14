<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationPreinscription extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $prenom, $nom, $sexe, $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->prenom = $user->prenom;
        $this->nom = $user->nom;
        $this->sexe = $user->sexe;
        $this->url = route('get_confirm_preregistration', ['token' => $user->uid]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject(__("You've been invited to Notorix!"));
        return $this->view('emails.invitation-preinscription');
    }
}
