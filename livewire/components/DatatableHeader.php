<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Livewire\Attributes\On;

class DatatableHeader extends Component
{
    public $moduleId = null;
    public $columns = [];
    private $application;
    private $module;
    public $checkDeclaration = true;
    public $sort = '';
    public $perPage = 10;
    public $tableId = null;

    private function makeApplication()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, $this->checkDeclaration);
        $this->perPageOptions = $this->module->perPage();
        $this->perPage = $this->perPageOptions[0];
    }

    public function getListeners()
    {
        return [
            'table:perPage-' . $this->tableId => 'setPerPage'
        ];
    }

    public function setPerPage($perPage)
    {
        $this->perPage = +$perPage;
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
    }

    public function clickedSort($name, $oldName, $oldDirection)
    {
        $newDirection = "desc";
        $newName = $name;
        if ($oldName == $name) {
            $newDirection = $oldDirection == "desc" ? "asc" : "desc";
        }
        $this->sort = "{$newName}|{$newDirection}";
        $this->dispatch('table:sort-' . $this->tableId, $this->perPage, $this->sort);
    }

    public function render()
    {
        return view('supernova-livewire-views::datatable.header');
    }
}
