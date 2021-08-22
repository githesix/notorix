<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FoPaveGuest extends Component
{
    public User $u;

    public function mount()
    {
        $this->u = Auth::user();
    }

    public function render()
    {
        return view('livewire.fo-pave-guest');
    }
}
