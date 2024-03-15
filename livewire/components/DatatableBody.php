<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Facades\Blade;

class DatatableBody extends Component
{
    public $moduleId = null;
    public $columns = [];
    private $application;
    private $module;
    public $checkDeclaration = true;
    public $sort = '';
    public $filters  = [];
    public $hasItems = false;
    public $canCreate = false;
    public $perPage = 10;
    public $colspan = 1;
    public $perPageOptions = [10, 25, 50, 100];
    public $queryInit = '';
    public $searchText = '';
    public $cursor = null;
    public $moduleUrl = '';
    public $loaded = false;
    public $totalPages = 0;
    public $tableId = null;

    public function placeholder()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, $this->checkDeclaration);
        $this->columns = array_map(function ($row) {
            $row = (array)$row;
            $row["action"] = null;
            $row["action"] = null;
            $row["filter_options"] = null;
            $row["filter_options_callback"] = null;
            $row["filter_callback"] = null;
            return $row;
        }, $this->module->getDataTableVisibleColumns());
        $this->colspan = count($this->columns);
        $content = view('supernova-livewire-views::skeleton', ['size' => '300px'])->render();
        $colspan = $this->colspan;
        return <<<BLADE
             <tr class="bg-white dark:bg-gray-500">
                <td colspan="{{ $colspan }}" class="p-0">
                    <div class="w-full flex">
                        $content
                    </div>
                </td>
            </tr>
        BLADE;
    }

    private function makeApplication()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, $this->checkDeclaration);
        $this->canCreate = $this->module->canCreate();
        $this->perPageOptions = $this->module->perPage();
        $this->perPage = $this->perPageOptions[0];
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
        $this->colspan = count($this->columns);
    }

    public function getListeners()
    {
        return [
            'table:loadCursor-' . $this->tableId => 'loadCursor',
            'table:sort-' . $this->tableId => 'tableSort',
            'table:perPage-' . $this->tableId => 'setPerPage',
            'table:filterUpdated-' . $this->tableId => 'setFilter',
            'table:globalFilterUpdated-' . $this->tableId => 'setGlobalFilter',
            'filter-selected-' . $this->tableId => 'fieldSelected',
            'filter-updated-' . $this->tableId => 'filterUpdated',
            'filter-removed-' . $this->tableId => 'removeFilterOption',
        ];
    }

    public function tableSort($perPage, $sort)
    {
        $this->perPage = $perPage;
        $this->sort = $sort;
        $this->cursor = null;
        $this->loadData($this->perPage);
    }

    public function loadCursor($value, $perPage)
    {
        $this->cursor = $value;
        $this->perPage = $perPage;
        $this->loadData($this->perPage);
    }

    public function setPerPage($perPage)
    {
        $this->cursor = null;
        $this->perPage = +$perPage;
        $this->loadData($this->perPage);
    }

    public function setFilter($perPage, $sort, $filters)
    {
        $this->cursor = null;
        $this->perPage = +$perPage;
        $this->sort = $sort;
        $this->filters = $filters;
        $this->loadData($this->perPage);
    }

    public function setGlobalFilter($perPage, $sort, $searchText)
    {
        $this->cursor = null;
        $this->perPage = +$perPage;
        $this->sort = $sort;
        $this->searchText = $searchText;
        $this->loadData($this->perPage);
    }

    public function fieldSelected($values, $perPage, $sort)
    {
        $this->perPage = $perPage;
        $this->sort = $sort;
        $this->filters[data_get($values, 'index')][] = data_get($values, 'value');
        $this->loadData($this->perPage);
    }

    public function filterUpdated($filters, $perPage, $sort)
    {
        $this->filters = $filters;
        $this->perPage = $perPage;
        $this->sort = $sort;
        $this->loadData($this->perPage);
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
        $this->loadData($this->perPage);
    }

    public function loadData($perPage)
    {
        $this->perPage = $perPage;
        $this->makeApplication();
        $sort = explode("|", $this->sort);
        if (!$this->cursor) {
            $this->dispatch("table:setCurrentPage-" . $this->tableId, 1);
        }
        $query = $this->module->applyFilters($this->module->makeModel($this->queryInit), $this->searchText, $this->filters, $sort);
        $total = $query->count();
        $items = $query->cursorPaginate($perPage, ['*'], 'cursor', Cursor::fromEncoded($this->cursor));
        $this->hasNextCursor = $items->hasMorePages();
        $this->nextCursor = $this->hasNextCursor  ? $items->nextCursor()->encode() : null;
        $this->hasPrevCursor = $items->previousCursor() != null;
        $this->prevCursor = $this->hasPrevCursor ? $items->previousCursor()->encode() : null;
        $this->itemsPage = $this->processItems($items);
        $this->totalPages =  ceil($total / $perPage);
        $this->totalPages = $this->totalPages == 0 ? 1 : $this->totalPages;
        $this->totalResults = $total;
        $this->hasItems = $total > 0;
        $this->loaded = true;
        $this->dispatch("table:setTotalPages-" . $this->tableId, $this->totalPages);
        $this->dispatch("table:setTotalResults-" . $this->tableId, $this->totalResults);
        $this->dispatch("table:setPrevCursor-" . $this->tableId, $this->prevCursor);
        $this->dispatch("table:setNextCursor-" . $this->tableId, $this->nextCursor);
        $this->dispatch("table:setHasNextCursor-" . $this->tableId, $this->hasNextCursor);
        $this->dispatch("table:setHasPrevCursor-" . $this->tableId, $this->hasPrevCursor);
    }

    private function processItems($items): array
    {
        $columns = $this->module->getDataTableVisibleColumns();
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

    public function render()
    {
        $itemsPage = $this->loaded ? $this->itemsPage : [];
        $moduleUrl = $this->moduleUrl;
        return view('supernova-livewire-views::datatable.body', compact('itemsPage', 'moduleUrl'));
    }
}
