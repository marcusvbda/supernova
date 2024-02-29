<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;

class Dashboard extends Component
{
    private function renderCards($cards)
    {
        $columns = implode("", $cards);
        $content = <<<HTML
            <div class="grid lg:grid-cols-4 md:grid-cols-3 gap-3">
                $columns
            </div>
        HTML;
        return $content;
    }

    public function render()
    {
        $application = app()->make(config("supernova.application", Application::class));
        $metrics = $application->dashboardContent();
        $cardsContent = $this->renderCards(data_get($metrics, "cards", []));

        return <<<BLADE
           <section class="flex flex-col gap-8">
                $cardsContent
            </section>
        BLADE;
    }
}
