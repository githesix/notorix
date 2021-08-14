<?php

namespace App\Http\Livewire;

use Githesix\NotorixPreinscriptions\Models\Bleu;

class BleuAutocomplete extends Autocomplete
{
    protected $listeners = ['valueSelected'];

    public function valueSelected(Bleu $bleu)
    {
        $this->emit('bleuSelected', $bleu);
    }

    public function query() 
    {
        return Bleu::where('prenom', 'like', '%'.$this->search.'%')
        ->orWhere('nom', 'like', '%'.$this->search.'%')
        ->orderBy('nom');
    }
}
