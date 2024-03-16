<?php

namespace marcusvbda\supernova\livewire\components;

use Livewire\Component;

class Alerts extends Component
{
    public $alerts = [];

    public function getListeners()
    {
        return [
            "quick:alert" => 'setAlert'
        ];
    }

    public function mount()
    {
        $this->alerts = session('quick.alerts') ?? [];
    }

    public function setAlert($type, $message)
    {
        $this->alerts = [["type" => $type, "message" => $message]];
    }

    public function render()
    {
        session()->forget('quick.alerts');
        return view('supernova-livewire-views::alerts');
    }
}
