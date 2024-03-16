<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CrudSelectField extends Component
{
    public $type;
    public $index;
    public $value = null;
    public $options = [];
    public $selected = [];
    public $disabled = false;
    public $limit;
    public $crudId;
    public $entity = null;
    public $moduleId;
    private $module;
    private $application;
    public $loading = false;
    public $loaded = false;

    public function getListeners()
    {
        $index = $this->index;
        return [
            "crud:setFieldsLoading-" . $this->crudId => 'setLoading',
            "crud:updateField[$index]-" . $this->crudId => 'validateField',
        ];
    }

    public function rules()
    {
        $this->makeApplication();
        $field = $this->module->getField($this->index);
        return [
            'selected' => data_get($field, "rules", [])
        ];
    }

    public function messages()
    {
        $this->makeApplication();
        $field = $this->module->getField($this->index);
        $messages = [];
        foreach ($field->messages as $key => $value) {
            $messages["selected"] = $value;
        }
        return $messages;
    }

    public function validationAttributes()
    {
        $this->makeApplication();
        $field = $this->module->getField($this->index);
        return [
            'selected' => "<strong>" . strtolower($field->label) . "</strong>"
        ];
    }

    public function setLoading($data)
    {
        $this->loading = $data;
    }

    private function makeApplication()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, false);
    }

    public function updatedValue($value)
    {
        $index = $this->index;
        $this->selected[] = $value;
        $this->value = null;
        $this->dispatch('crud:updateField-' . $this->crudId, $this->index, $this->selected);
        $this->dispatch("crud:updateField[$index]-" . $this->crudId);
    }

    public function validateField()
    {
        $this->validateOnly('selected');
    }

    public function removeOption($i)
    {
        $index = $this->index;
        $this->selected = array_values(array_filter($this->selected, function ($index) use ($i) {
            return $index != $i;
        }));
        $this->dispatch('crud:updateField-' . $this->crudId, $this->index, $this->selected);
        $this->dispatch("crud:updateField[$index]-" . $this->crudId);
    }

    public function loadOptions()
    {
        $this->makeApplication();
        $field = $this->module->getField($this->index);
        $options_callback = $field->options_callback;
        if ($options_callback && is_callable($options_callback)) {
            $this->options =  $options_callback();
        } else {
            $this->options = $field->options;
        }
        $this->loaded = true;
    }

    public function mount()
    {
        if ($this->entity) {
            $value = data_get($this->entity, $this->index);
            $isCollection = $value instanceof Collection;
            if ($isCollection) {
                $value = $value->pluck("id")->toArray();
            } else {
                $value = is_array($value) ? $value : [$value];
            }
            $this->selected = $value;
        }
    }

    public function render()
    {
        return view('supernova-livewire-views::crud.live-fields.select-field-component');
    }
}
