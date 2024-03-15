<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Livewire\Attributes\On;

class DatatableHeaderFilter extends Component
{
    public $moduleId = null;
    public $columns = [];
    private $application;
    private $module;
    public $checkDeclaration = true;
    public $filters  = [];
    public $filterable = false;
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

    public function clearFilter($field)
    {
        data_set($this->filters, $field, null);
        $this->dispatch('filter-updated', $this->filters, $this->perPage, $this->sort);
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
        $this->filterable = collect($this->columns)->filter(fn ($row) => $row["filterable"])->count() > 0;
    }

    public function updated($field)
    {
        if (str_starts_with($field, "filters.")) {
            $this->dispatch('filter-updated', $this->filters, $this->perPage, $this->sort);
        }
    }

    public function render()
    {
        return view('supernova-livewire-views::datatable.header-filter');
    }
}
