<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Auth;

class Navbar extends Component
{
    public $items;
    public $currentUrl;
    public $logo;
    public $homeRoute;
    public $menuUserNavbar;

    public function __construct()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
    }

    private function makeSettings(): void
    {
        $this->items = $this->application->menuItems();
        $this->currentUrl = request()->url();
        $this->logo = $this->application->logo();
        $this->homeRoute = route("supernova.home");
        $this->menuUserNavbar = $this->application->menuUserNavbar();
    }

    public function render()
    {
        if (!Auth::check()) return <<<BLADE
            <div></div>
        BLADE;
        $this->makeSettings();
        return view('supernova-livewire-views::navbar');
    }
}
