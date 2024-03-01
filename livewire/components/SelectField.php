<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;

class SelectField extends Component
{
    public $index;
    public $all_is_selected;
    public $limit;
    public $selected;
    public $option_size;
    public $options = [];
    public $loaded_options = false;
    public $moduleId;
    public $type;
    public $entity = null;
    public $crudType = 'details';

    public function render()
    {
        return view('supernova-livewire-views::select-field');
    }

    public function loadOptions()
    {
        $module = $this->getAppModule();
        if ($this->type === "filter") {
            $columns = $module->getDataTableVisibleColumns();
            $column = collect($columns)->first(fn ($col) => $col->name == $this->index);
            $filter_options_callback = $column->filter_options_callback;
            if ($filter_options_callback && is_callable($filter_options_callback)) {
                $this->options = $filter_options_callback();
            } else {
                $this->options = $column->filter_options;
            }
        } else {
            $fields = $this->allFields();
            $field = collect($fields)->first(function ($f) {
                return $f->field == $this->index;
            });
            $options_callback = $field->options_callback;
            if ($options_callback && is_callable($options_callback)) {
                $this->options = $options_callback();
            } else {
                $this->options = $field->options;
            }
        }
        $this->loaded_options = true;
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
        $this->dispatch($this->type . "-selected", [
            "value" => $value,
            "index" => $this->index
        ]);
        $this->selected[] = $value;
    }

    public function removed($value)
    {
        $this->dispatch($this->type . "-removed", [
            "value" => $value,
            "index" => $this->index
        ]);
        $this->selected = collect($this->selected)->filter(fn ($item) => $item != $value)->toArray();
    }
}
