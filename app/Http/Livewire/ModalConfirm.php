<?php

namespace App\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class ModalConfirm extends ModalComponent
{
    public $title, $body, $datas, $callback;

    public function mount($title, $body, $datas, $callback)
    {
        $this->title = $title;
        $this->body = $body;
        $this->datas = $datas;
        $this->callback = $callback;
    }

    public function render()
    {
        return view('livewire.modal-confirm');
    }

    public function confirm()
    {
        $this->emit($this->callback, $this->datas);
        $this->closeModal();
    }

}
