<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Livewire\Attributes\On;

class SelectField extends Component
{
    public $index;
    public $limit;
    public $selected = [];
    public $all_is_selected;
    public $option_size;
    public $options = null;
    public $moduleId;
    public $type;
    public $entity = null;
    public $disabled = false;
    public $lazy = true;
    public $loading = false;
    public $crudType = 'details';
    public $perPage = 10;
    public $sort = '';
    public $refId = null;
    public $tableId = '';

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

    public function placeholder()
    {
        return view('supernova-livewire-views::skeleton', ['size' => '38px']);
    }

    public function render()
    {
        $this->loadOptions();
        return view('supernova-livewire-views::select-field');
    }

    public function loadOptions()
    {
        if (is_array($this->options)) return;
        $module = $this->getAppModule();
        if ($this->type === "filter") {
            $columns = $module->getDataTableVisibleColumns();
            $column = collect($columns)->first(fn ($col) => $col->name == $this->index);
            $filter_options_callback = $column->filter_options_callback;
            $options = [];
            if ($filter_options_callback && is_callable($filter_options_callback)) {
                $options =   $filter_options_callback();
            } else {
                $options =  $column->filter_options;
            }
            $this->dispatch("table:setFilterOptions-" . $this->tableId, $this->index, $options);
        } else {
            $fields = $this->allFields();
            $field = collect($fields)->first(function ($f) {
                return $f->field == $this->index;
            });
            $options_callback = $field->options_callback;
            if ($options_callback && is_callable($options_callback)) {
                $this->options =  $options_callback();
            } else {
                $this->options = $field->options;
            }
        }
    }

    public function allFields()
    {
        $module = $this->getAppModule();
        $fields = $module->fields(@$this->entity, $this->crudType);
        $result = [];
        foreach ($fields as $field) {
            $subfields = data_get($field, "fields");
            if (!$subfields) {
                $result[] = $field;
            } else {
                foreach (data_get($field, "fields", []) as $subfield) {
                    $result[] = $subfield;
                }
            }
        }
        return $result;
    }

    private function getAppModule()
    {
        $application = app()->make(config("supernova.application", Application::class));
        return $application->getModule($this->moduleId, false);
    }

    public function changed($value)
    {
        $this->dispatch($this->type . "-selected-" . $this->refId, [
            "value" => $value,
            "index" => $this->index,
        ], $this->perPage, $this->sort);
        $this->selected[] = $value;
    }

    public function removed($value)
    {
        $this->dispatch($this->type . "-removed-" . $this->refId, [
            "value" => $value,
            "index" => $this->index
        ], $this->perPage, $this->sort);
        $this->selected = collect($this->selected)->filter(fn ($item) => $item != $value)->toArray();
    }
}
