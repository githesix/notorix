<?php

namespace App\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class FoSubscribeAsTeacherModal extends ModalComponent
{
    public $code;
    
    public function render()
    {
        return view('livewire.fo-subscribe-as-teacher-modal');
    }
}
