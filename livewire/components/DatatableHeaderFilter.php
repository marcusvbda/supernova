<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;

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
    public $tableId = null;

    public function getListeners()
    {
        return [
            'table:perPage-' . $this->tableId => 'setPerPage',
            'table:sort-' . $this->tableId => 'setSort',
            'filter-selected-' . $this->tableId => 'fieldSelected',
            'filter-removed-' . $this->tableId => 'removeFilterOption',
        ];
    }

    public function removeFilterOption($values, $perPage, $sort)
    {
        $this->perPage = $perPage;
        $this->sort = $sort;
        $field = data_get($values, 'index');
        $value = data_get($values, 'value');
        $oldValues = data_get($this->filters, $field, []);
        $newValues = collect($oldValues)->filter(fn ($item) => $item != $value);
        $this->filters[$field] = $newValues->count() > 0 ? $newValues->toArray() : [];
    }

    public function fieldSelected($values, $perPage, $sort)
    {
        $this->perPage = $perPage;
        $this->sort = $sort;
        $this->filters[data_get($values, 'index')][] = data_get($values, 'value');
    }

    public function setSort($perPage, $sort)
    {
        $this->sort = $sort;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = +$perPage;
    }

    public function clearFilter($field)
    {
        data_set($this->filters, $field, null);
        $this->dispatch('filter-updated-' . $this->tableId, $this->filters, $this->perPage, $this->sort);
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
            $this->dispatch('filter-updated-' . $this->tableId, $this->filters, $this->perPage, $this->sort);
        }
    }

    public function render()
    {
        return view('supernova-livewire-views::datatable.header-filter');
    }
}
