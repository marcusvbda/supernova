<?php

namespace marcusvbda\supernova\livewire\components;

use Livewire\Component;

class CrudCustomComponent extends Component
{
    public $index;
    public $crudId;
    public $component = null;
    public $moduleId;
    public $entity = null;
    public $values = [];

    public function getListeners()
    {
        return [
            "crud:updateField-" . $this->crudId => "fieldUpdate",
            "crud:setValues-" . $this->crudId => "setValues",
        ];
    }

    public function fieldUpdate($index, $value)
    {
        $this->values[$index] = $value;
    }

    public function setValues($values)
    {
        $this->values = $values;
    }

    public function render()
    {
        return view('supernova-livewire-views::crud.live-fields.custom-component');
    }
}
