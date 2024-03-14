<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Facades\Blade;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;

#[Lazy]
class Datatable extends Component
{
    public $module;
    public $icon;
    public $name;
    public $canCreate;
    public $searchText;
    public $filterable;
    public $searchable;
    public $columns;
    public $sort;
    public $itemsPage = [];
    public $perPageOptions = [];
    public $loaded_options = [];
    public $filter_options = [];
    public $perPage;
    public $hasPrevCursor = false;
    public $hasNextCursor = false;
    public $cursor = null;
    public $prevCursor = null;
    public $nextCursor = null;
    public $currentPage = 1;
    public $totalPages = 1;
    public $totalResults = 0;
    public $filters  = [];
    public $hasItems = false;
    public $checkDeclaration = true;
    public $btnCreateText = "Create";
    public $moduleUrl = "/";
    public $queryInit = null;
    public $parentId = null;
    public $uniqueId = null;

    public function updateFilterValue($field, $value, $label, $type)
    {
        if (str_starts_with($type, 'multiple')) {
            $oldValues = data_get($this->filters, $field, []);
            if (!collect($oldValues)->contains(fn ($item) => $item['value'] == $value)) {
                $this->filters[$field][] = ['value' => $value, 'label' => $label];
            }
        } else {
            $this->filters[$field] = $value;
        }
    }

    public function placeholder()
    {
        return view('supernova-livewire-views::skeleton', ['size' => '500px', 'class' => 'mt-4']);
    }

    private function initializeModule()
    {
        $module = $this->getAppModule();
        $this->name = $module->name();
        $this->canCreate = $module->canCreate();
        $this->columns = array_map(function ($row) {
            $row = (array)$row;
            $row["action"] = null;
            $row["action"] = null;
            $row["filter_options"] = null;
            $row["filter_options_callback"] = null;
            $row["filter_callback"] = null;
            return $row;
        }, $module->getDataTableVisibleColumns());
        $this->searchable = collect($this->columns)->filter(fn ($row) => $row["searchable"])->count() > 0;
        $this->filterable = collect($this->columns)->filter(fn ($row) => $row["filterable"])->count() > 0;
        $this->btnCreateText = $module->createBtnText();
        if ($this->queryInit) {
            $urlSplitted = explode(".", $this->queryInit);
            $this->moduleUrl = route("supernova.modules.field-create", ['module' => $urlSplitted[0], 'id' => $urlSplitted[1], 'field' => $urlSplitted[2]]);
            $this->moduleUrl = str_replace("/create", "", $this->moduleUrl);
        } else {
            $this->moduleUrl = route("supernova.modules.index", $this->module);
        }
        $this->perPageOptions = $module->perPage();
    }

    private function getAppModule()
    {
        $application = app()->make(config("supernova.application", Application::class));
        return $application->getModule($this->module, $this->checkDeclaration);
    }

    public function reloadSort($name, $oldName, $oldDirection)
    {
        $newDirection = "desc";
        $newName = $name;
        if ($oldName == $name) {
            $newDirection = $oldDirection == "desc" ? "asc" : "desc";
        }
        $this->sort = "{$newName}|{$newDirection}";
        $this->cursor = null;
        $this->loadData();
    }

    public function setCursor($cursor, $type)
    {
        $this->cursor = $cursor;
        if ($type == "prev") {
            $this->currentPage--;
        } else {
            $this->currentPage++;
        }
        $this->loadData();
    }

    public function loadData()
    {
        $this->initializeModule();
        $sort = explode("|", $this->sort);
        $module = $this->getAppModule();
        $this->perPage = in_array($this->perPage, $this->perPageOptions) ? $this->perPage : $this->perPageOptions[0];
        $query = $module->applyFilters($module->makeModel($this->queryInit), $this->searchText, $this->filters, $sort);
        $total = $query->count();
        $items = $query->cursorPaginate($this->perPage, ['*'], 'cursor', Cursor::fromEncoded($this->cursor));
        $this->hasNextCursor = $items->hasMorePages();
        $this->nextCursor = $this->hasNextCursor  ? $items->nextCursor()->encode() : null;
        $this->hasPrevCursor = $items->previousCursor() != null;
        $this->prevCursor = $this->hasPrevCursor ? $items->previousCursor()->encode() : null;
        $this->itemsPage = $this->processItems($items);
        $this->totalPages =  ceil($total / $this->perPage);
        $this->totalPages = $this->totalPages == 0 ? 1 : $this->totalPages;
        $this->totalResults = $total;
        $this->hasItems = $total > 0;
        $this->dispatch($this->uniqueId . "-refreshed");
    }

    public function removeFilter($field, $value)
    {
        $oldValues = data_get($this->filters, $field, []);
        $newValues = collect($oldValues)->filter(fn ($item) => $item['value'] != $value);
        $this->filters[$field] = $newValues->count() > 0 ? $newValues->toArray() : [];
        $this->loadData();
    }

    public function updated($field)
    {
        if (str_starts_with($field, "filters.") || $field === "searchText") {
            $this->currentPage = 1;
            $this->cursor = null;
            $this->loadData();
        }
    }

    private function processItems($items): array
    {
        $module = $this->getAppModule();
        $columns = $module->getDataTableVisibleColumns();
        $itemsPage = [];
        foreach ($items as $item) {
            $rowColumns = [];
            foreach ($columns as $column) {
                $action = $column->action;
                $rowColumns[$column->name] = $this->executeAction($action, $item);
            }
            $rowColumns["_id"] = @$item->id;
            $itemsPage[] = $rowColumns;
        }

        return $itemsPage;
    }

    #[On('filter-selected')]
    public function fieldSelected($values)
    {
        $this->filters[data_get($values, 'index')][] = data_get($values, 'value');
    }

    public function loadFilterOptions($field)
    {
        $module = $this->getAppModule();
        $columns = $module->getDataTableVisibleColumns();
        $column = collect($columns)->first(fn ($col) => $col->name == $field);
        $filter_options_callback = $column->filter_options_callback;
        if ($filter_options_callback && is_callable($filter_options_callback)) {
            $this->filter_options[$field] = $filter_options_callback();
        } else {
            $this->filter_options[$field] = $column->filter_options;
        }
        $this->loaded_options[$field] = true;
    }

    private function executeAction($action, $item)
    {
        $noData = config("supernova.placeholder_no_data", "<span>   -   </span>");
        if (is_callable($action)) {
            $result = @$action($item);
            return Blade::render($result ? "<span>$result</span>" : $noData);
        } elseif (is_string($action) || is_numeric($action)) {
            return Blade::render($action);
        }
        return Blade::render($noData);
    }

    public function clearFilter($field)
    {
        data_set($this->filters, $field, null);
        $this->loadData();
    }

    public function clearSearch()
    {
        $this->searchText = "";
        $this->loadData();
    }

    #[On('filter-removed')]
    public function removeFilterOption($values)
    {
        $field = data_get($values, 'index');
        $value = data_get($values, 'value');
        $oldValues = data_get($this->filters, $field, []);
        $newValues = collect($oldValues)->filter(fn ($item) => $item != $value);
        $this->filters[$field] = $newValues->count() > 0 ? $newValues->toArray() : [];
        $this->loadData();
    }

    public function render()
    {
        $this->loadData();
        return view('supernova-livewire-views::datatable.index');
    }

    public function mount()
    {
        $this->uniqueId = uniqid();
    }
}
