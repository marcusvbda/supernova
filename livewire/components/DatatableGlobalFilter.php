<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Livewire\Attributes\On;

class DatatableGlobalFilter extends Component
{
    public $moduleId = null;
    public $searchable = false;
    public $text = "";
    public $columns = [];
    private $application;
    private $module;
    public $checkDeclaration = true;
    public $perPage = 10;
    public $sort = '';

    #[On('table:sort')]
    public function setSort($perPage, $sort)
    {
        $this->sort = $sort;
    }

    #[On('table:perPage')]
    public function setPerPage($perPage)
    {
        $this->perPage = +$perPage;
    }

    public function clearSearch()
    {
        $this->text = "";
        $this->dispatch('table:globalFilterUpdated', $this->perPage, $this->sort, $this->text);
    }

    private function makeApplication()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, $this->checkDeclaration);
    }

    public function mount()
    {
        $this->makeApplication();
        $this->columns = array_map(function ($row) {
            $row = (array)$row;
            $row["action"] = null;
            $row["action"] = null;
            $row["filter_options"] = null;
            $row["filter_options_callback"] = null;
            $row["filter_callback"] = null;
            return $row;
        }, $this->module->getDataTableVisibleColumns());
        $this->searchable = collect($this->columns)->filter(fn ($row) => $row["searchable"])->count() > 0;
    }

    public function updatedText()
    {
        $this->dispatch('table:globalFilterUpdated', $this->perPage, $this->sort, $this->text);
    }

    public function render()
    {
        return view('supernova-livewire-views::datatable.filter');
    }
}
