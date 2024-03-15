<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Livewire\Attributes\On;

class DatatablePagination extends Component
{
    public $moduleId = null;
    private $module;
    public $perPageOptions = [10, 25, 50, 100];
    public $perPage = 10;
    public $totalPages = 1;
    public $totalResults = 0;
    public $currentPage = 1;
    public $prevCursor = null;
    public $hasPrevCursor = false;
    public $nextCursor = null;
    public $hasNextCursor = false;
    public $cursor = null;

    public function nextPage($cursor)
    {
        $this->currentPage++;
        $this->cursor = $cursor;
        $this->dispatch("table:loadCursor", $this->cursor, $this->perPage);
    }

    public function previousPage($cursor)
    {
        $this->currentPage--;
        $this->cursor = $cursor;
        if ($this->currentPage == 1) $this->cursor = null;
        $this->dispatch("table:loadCursor", $this->cursor, $this->perPage);
    }

    private function makeApplication()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, false);
        $this->perPageOptions = $this->module->perPage();
        $this->perPage = $this->perPageOptions[0];
    }

    #[On('table:setHasNextCursor')]
    public function setHasNextCursor($hasNextCursor)
    {
        $this->hasNextCursor = $hasNextCursor;
    }

    #[On('table:setHasPrevCursor')]
    public function setHasPrevCursor($hasPrevCursor)
    {
        $this->hasPrevCursor = $hasPrevCursor;
    }

    #[On('table:setNextCursor')]
    public function setNextCursor($nextCursor)
    {
        $this->nextCursor = $nextCursor;
    }

    #[On('table:setPrevCursor')]
    public function setPrevCursor($prevCursor)
    {
        $this->prevCursor = $prevCursor;
    }

    #[On('table:setTotalResults')]
    public function setTotalResults($totalResults)
    {
        $this->totalResults = $totalResults;
    }

    #[On('table:setTotalPages')]
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    #[On('table:setCurrentPage')]
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        if ($this->currentPage === 1) $this->cursor = null;
    }

    public function mount()
    {
        $this->makeApplication();
        $this->dispatch("table:loadData");
    }

    public function updatedPerPage($value)
    {
        $this->currentPage = 1;
        $this->dispatch("table:perPage", $value);
    }

    public function render()
    {
        return view('supernova-livewire-views::datatable.pagination');
    }
}
