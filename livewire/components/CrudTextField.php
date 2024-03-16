<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;

class CrudTextField extends Component
{
    public $type;
    public $index;
    public $value = "";
    public $mask = "";
    public $disabled = false;
    public $crudId;
    public $rows = 3;
    public $moduleId;
    public $entity = null;
    private $module;
    private $application;
    public $loading = false;

    public function getListeners()
    {
        return [
            "crud:setFieldsLoading-" . $this->crudId => 'setLoading',
        ];
    }

    public function setLoading($data)
    {
        $this->loading = $data;
    }

    public function rules()
    {
        $this->makeApplication();
        $field = $this->module->getField($this->index);
        return [
            'value' => data_get($field, "rules", [])
        ];
    }

    public function messages()
    {
        $this->makeApplication();
        $field = $this->module->getField($this->index);
        $messages = [];
        foreach ($field->messages as $key => $value) {
            $messages["value"] = $value;
        }
        return $messages;
    }

    public function validationAttributes()
    {
        $this->makeApplication();
        $field = $this->module->getField($this->index);
        return [
            'value' => "<strong>" . strtolower($field->label) . "</strong>"
        ];
    }

    public function updatedValue($value)
    {
        $this->dispatch('crud:updateField-' . $this->crudId, $this->index, $value);
        $this->validateOnly('value');
    }

    private function makeApplication()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, false);
    }

    public function mount()
    {
        if ($this->entity) {
            $this->value = data_get($this->entity, $this->index);
        }
    }

    public function render()
    {
        return view('supernova-livewire-views::crud.live-fields.text-field-component');
    }
}
