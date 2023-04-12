<?php

namespace App\Http\Livewire;

use App\Models\Eleve;
use App\Models\Groupe;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use LivewireUI\Modal\ModalComponent;

class BoModalEditUser extends ModalComponent
{
    public User $user;
    public $bitroles;
    public $roles;
    public $groupes, $checkedgroups = [], $classes, $checkedclasses = [];
    protected $listeners = ['cancelVerification', 'resetPasswordModal', 'refreshGroups', 'addKid', 'attachElu', 'syncClasses'];

    public function rules()
    {
        return [
            'user.sexe' => 'required',
            'user.prenom' => 'required',
            'user.nom' => 'required',
            'user.username' => 'required|email|unique:users,username,'.$this->user->id,
            'user.iban' => 'nullable|iban',
            'user.tel1' => 'nullable|max:16',
            'user.tel2' => 'nullable|max:16',
        ];
    }

    public function mount($id)
    {
        $this->user = User::withTrashed()->find($id);
        $this->bitroles = $this->user->tabRoles();
        foreach ($this->bitroles as $key => $value) {
            $this->roles[$key] = $this->user->role & $key ? true : false;
        }
        $this->groupes = Groupe::orderBy('nom', 'asc')->get(['id', 'nom', 'description']);
        $this->checkedgroups = $this->user->groupes->pluck('id')->map(function($item, $key){return strval($item);})->toArray();
        $this->classes = $this->user->classes()->orderBy('libelle', 'asc')->get();
        $this->checkedclasses = $this->user->classes->pluck("id")->map(function($item, $key){return strval($item);})->toArray();
    }

    public function render()
    {
        return view('livewire.bo-modal-edit-user');
    }

    public function submit()
    {
        $this->user->name = $this->user->prenom . " " . $this->user->nom;
        $role = 0;
        foreach ($this->roles as $key => $value) {
            $role += $value ? $key : 0;
        }
        $this->user->role = $role;
        $this->user->groupes()->sync($this->checkedgroups);
        $this->user->save();
        $this->emit('forcerefreshLivewireDatatable');
        $this->closeModal();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function resendemailverification()
    {
        $this->user->sendEmailVerificationNotification();
        $this->emit('validationlinksent');
    }

    public function cancelVerification($datas)
    {
        $id = $datas['id'];
        $this->user->email_verified_at = null;
        $this->user->save();
        info(__(":admin cancelled email verification for :user (:email)", ["admin" => Auth::user()->name, "user" => $this->user->name, "email" => $this->user->email]));
    }

    /**
     * "datas" => ["id" => $user->id, "name" => $user->name, "email" => $user->username] (resources/views/livewire/bo-modal-edit-user.blade.php)
     */
    public function resetPasswordModal($datas)
    {
        $email = $datas['email'];
        $response = Password::sendResetLink(['email'=>$email]);
        if ($response == Password::RESET_LINK_SENT) {
            info(__(':admin sent password reset link to :user', ["admin" => Auth::user()->name, "user" => $email]));
            return true;
        } else {
            info('ERROR: ' . __(':admin had a problem sending reset password link to :user', ["admin" => Auth::user()->name, "user" => $email]));
            return false;
        }
    }

    public function refreshGroups()
    {
        $this->groupes = Groupe::orderBy('nom', 'asc')->get(['id', 'nom', 'description']);
        $this->checkedgroups = $this->user->groupes->pluck('id')->map(function($item, $key){return strval($item);})->toArray();
    }

    public function addKid($eleveid)
    {
        $this->user->kids()->detach($eleveid);
        $this->user->kids()->attach($eleveid);
        $this->user->setRole(4,1);
        $this->user->save();
        $this->mount($this->user->id);
        $this->render();
    }

    public function saveRole()
    {
        $role = 0;
        foreach ($this->roles as $key => $value) {
            $role += $value ? $key : 0;
        }
        $this->user->role = $role;
        $this->user->save();
        $this->emit('forcerefreshLivewireDatatable');
    }

    public function detachKid($eleveid)
    {
        $this->user->kids()->detach($eleveid);
        if (count($this->user->kids) == 1) { // $this->user->kids has not yet been updated
            $this->user->setRole(4,0);
            $this->user->save();
        }
        $this->mount($this->user->id);
        $this->render();
    }

    public function detachElu($eleveid)
    {
        $this->user->elu = null;
        $this->user->setRole(2,0);
        $this->user->save();
        $this->mount($this->user->id);
        $this->render();
    }

    public function attachElu($eleveid)
    {
        $this->user->elu = $eleveid;
        $this->user->setRole(2,1);
        $this->user->save();
        $this->mount($this->user->id);
        $this->render();
    }

    public function detachClass($classeid)
    {
        $this->user->classes()->detach($classeid);
        $this->mount($this->user->id);
        $this->render();
    }

    public function syncClasses($selection)
    {
        $this->user->classes($selection);
        $this->user->save();
        $this->mount($this->user->id);
        $this->render();
    }

}
