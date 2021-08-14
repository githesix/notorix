<?php

namespace App\Http\Livewire;

use App\Models\Eleve;
use LivewireUI\Modal\ModalComponent;

class BoEleveSelectModal extends ModalComponent
{
    public $callback, $searcheleves, $foundeleves = [];

    public function render()
    {
        return view('livewire.bo-eleve-select-modal');
    }

    public function search()
    {
        if (strlen($this->searcheleves) > 2) {
            $s = '%'.strtolower($this->searcheleves).'%';
            $this->foundeleves = Eleve::where('prenom', 'like', $s)->orWhere('nom', 'like', $s)->get();
        } else {
            $this->foundeleves = [];
        }
    }

    public function select($eleveid)
    {
        $this->emit($this->callback, $eleveid);
        $this->closeModal();
    }
}
