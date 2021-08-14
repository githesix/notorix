<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use App\Models\Eleve;
use LivewireUI\Modal\ModalComponent;

class BoClasseSelectModal extends ModalComponent
{
    public $callback, $classes = [], $selection;

    public function mount()
    {
        $this->classes = Classe::orderBy('libelle', 'asc')->get(['id', 'libelle', 'titulaire']);
    }

    public function render()
    {
        return view('livewire.bo-classe-select-modal');
    }

    public function select($selection)
    {
        $this->emit($this->callback, $selection);
        $this->closeModal();
    }
}
