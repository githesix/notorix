<?php

namespace App\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class FoSubscribeAsEleveModal extends ModalComponent
{
    public $code;
    
    public function render()
    {
        return view('livewire.fo-subscribe-as-eleve-modal');
    }
}
