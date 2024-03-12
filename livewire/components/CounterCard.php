<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
class CounterCard extends Component
{
    public $module;

    public function placeholder()
    {
        return view('supernova-livewire-views::skeleton', ['size' => '136
        px']);
    }

    public function goToModule()
    {
        return redirect()->route("supernova.modules.index", ['module' => $this->module]);
    }

    public function render()
    {
        $application = app()->make(config("supernova.application", Application::class));
        $module = $application->getModule($this->module);
        $name = $module->name()[1];
        $content = $module->getQty();
        $cardCounterReloadTime = $application->cardCounterReloadTime();
        $actions = "wire:poll.{$cardCounterReloadTime}s wire:click='goToModule'";
        return <<<BLADE
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm w-full cursor-pointer" $actions>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-50 mb-3">
                    $name
                </h2>
                <p class="text-4xl font-bold text-gray-700 dark:text-gray-50 mb-2">
                    $content
                </p>
            </div>
        BLADE;
    }
}
