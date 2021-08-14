<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FoPaveRoles extends Component
{
    protected $listeners = ['syncClasses'];
    public User $u;
    public $checkedclasses = [];

    public function mount()
    {
        $this->u = Auth::user();
        $this->checkedclasses = $this->u->classes->pluck("id")->map(function($item, $key){return strval($item);})->toArray();
    }

    public function render()
    {
        return view('livewire.fo-pave-roles');
    }

    public function syncClasses($selection)
    {
        $this->u->classes($selection);
        $this->u->save();
        $this->mount();
        $this->render();
    }
}
