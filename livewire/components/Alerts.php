<?php

namespace marcusvbda\supernova\livewire\components;

use Livewire\Component;

class Alerts extends Component
{
    public $alerts = [];

    public function mount()
    {
        $this->alerts = session('quick.alerts') ?? [];
    }

    public function render()
    {
        session()->forget('quick.alerts');
        return view('supernova-livewire-views::alerts');
    }
}
