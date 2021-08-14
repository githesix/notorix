<?php

namespace App\Http\Livewire;

use App\Models\Groupe;
use App\Models\User;
use LivewireUI\Modal\ModalComponent;

class BoGroupAddModal extends ModalComponent
{

    public Groupe $groupe;
    public $userId = null;

    public function mount($userId = null)
    {
        $this->groupe = new Groupe();
        $this->userId = $userId;
    }

    public function rules()
    {
        return [
            'groupe.nom' => 'required|string|min:3|unique:groupes,nom',
            'groupe.description' => 'string|max:160',
        ];
    }

    public function render()
    {
        return view('livewire.bo-group-add-modal');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();
        $this->groupe->save();
        if ($this->userId) {
            $this->groupe->users()->attach($this->userId);
        }
        $this->emit('refreshGroups');
        $this->closeModal();
    }
}
