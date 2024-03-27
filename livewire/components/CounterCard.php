<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Livewire\Attributes\Lazy;

class CounterCard extends Component
{
    public $module;
    public $loading = true;
    public $name = '';
    public $qty = 0;
    public $cardCounterReloadTime = 0;

    public function loadData()
    {
        $application = app()->make(config("supernova.application", Application::class));
        $module = $application->getModule($this->module);
        $this->name = $module->name()[1];
        $this->cardCounterReloadTime = $application->cardCounterReloadTime();
        $this->qty = $module->getQty();
        $this->loading = false;
    }

    public function render()
    {
        if ($this->loading) return view('supernova-livewire-views::skeleton', ['size' => '136px', 'action' => "wire:init=loadData"]);
        $cardCounterReloadTime = $this->cardCounterReloadTime;
        $actions = "wire:poll.{$cardCounterReloadTime}s";
        $route = route("supernova.modules.index", ['module' => $this->module]);
        $name = $this->name;
        $content = $this->qty;
        return <<<BLADE
            <a href="$route" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm w-full cursor-pointer" $actions>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-50 mb-3">
                    $name
                </h2>
                <p class="text-4xl font-bold text-gray-700 dark:text-gray-50 mb-2">
                    $content
                </p>
            </a>
        BLADE;
    }
}
