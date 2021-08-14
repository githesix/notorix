<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    public $level;
    public $vue;

    public function __construct($level = "blue", $vue = false)
    {
        $this->level = config('perso.levels')[$level];
        $this->vue = $vue;
    }
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.app');
    }
}
