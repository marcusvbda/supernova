<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use marcusvbda\supernova\FIELD_TYPES;

class CrudHeader extends Component
{
    public $moduleId;
    public $checkDeclaration = false;
    public $entity = null;
    public $parentId = null;
    public $parentModule = null;
    public $crudType = "create";
    public $title = "";
    public $crudId;
    public $values = [];
    public $loading = false;

    public function getListeners()
    {
        return [
            "crud:setLoading-" . $this->crudId => 'setLoading',
            "crud:updateField-" . $this->crudId => "fieldUpdate",
            "crud:submit-" . $this->crudId => "submitForm"
        ];
    }

    public function fieldUpdate($index, $value)
    {
        $this->values[$index] = $value;
    }

    private function makeApplication()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, $this->checkDeclaration);
    }

    public function save()
    {
        $this->loading = true;
        $this->dispatch("crud:submit-" . $this->crudId);
    }

    public function submitForm()
    {
        $this->makeApplication();
        sleep(1);
        $this->dispatch("crud:save-" . $this->crudId);
        $this->dispatch("crud:setFieldsLoading-" . $this->crudId, true);
        $checkFields = $this->validateAllFields();
        if (!data_get($checkFields, "success", false)) {
            $messages = [];
            foreach ($checkFields["errors"] as $key => $value) {
                $messages[] = data_get($value, "0");
            }
            $this->dispatch("quick:alert", "error", implode("<br>", $messages));
            $this->dispatch("crud:setFieldsLoading-" . $this->crudId, false);
            return $this->loading = false;
        }
        $isField = $this->parentId && $this->parentModule;
        $values = ['save' => [], 'post_save' => []];

        $panels = $this->module->getVisibleFieldPanels('', $this->entity, $this->crudType);
        foreach ($panels as $panel) {
            foreach ($panel->fields as $field) {
                if ($field->type !== FIELD_TYPES::MODULE->value) {
                    if ($field->type == FIELD_TYPES::SELECT->value) {
                        if ($field->multiple) {
                            $values['post_save'][$field->field] = array_map(fn ($item) => $item, data_get($this->values, $field->field, []) ?? []);
                        } else {
                            $values['save'][$field->field] = data_get(data_get($this->values, $field->field, []), "0");
                        }
                    } else {
                        if ($field->type == FIELD_TYPES::UPLOAD->value) {
                            $values['save'][$field->field] = data_get($this->values, $field->field, []);
                        } else {
                            if (isset($this->values[$field->field])) {
                                $values['save'][$field->field] = $this->values[$field->field];
                            }
                        }
                    }
                }
            }
        }
        $id = $this->module->onSave(data_get($this->entity, 'id'), $values, ['type' => $this->crudType, 'parent_id' => $this->parentId, 'parent_module' => $this->parentModule]);
        $this->application::message("success", "Registro salvo com sucesso");
        if ($isField) {
            return redirect()->route('supernova.modules.details', ['module' => $this->parentModule, 'id' => $this->parentId]);
        }
        return redirect()->route('supernova.modules.details', ['module' => $this->module->id(), 'id' => $id]);
    }

    public function getAllVisibleFields()
    {
        $this->makeApplication();
        $fields = $this->module->fields($this->entity, $this->crudType);
        $visibleFields = [];
        foreach ($fields as $field) {
            if (data_get($field, "visible", true)) {
                $visibleFields[] = $field;
            }
        }
        return $visibleFields;
    }

    public function validateAllFields()
    {
        $this->makeApplication();
        $panels = $this->getAllVisibleFields();
        $rules = [];
        $messages = [];
        $attributes = [];
        foreach ($panels as $panel) {
            $fields = @$panel->fields ? $panel->fields : [$panel];
            foreach ($fields as $field) {
                if (!in_array($field->type, [FIELD_TYPES::MODULE->value, FIELD_TYPES::UPLOAD->value])) {
                    $index = $field->field;
                    $rules[$index] = data_get($field, "rules", []);
                    $attributes[$index] = "<strong>" . strtolower($field->label) . "</strong>";
                    if ($field->messages) {
                        foreach ($field->messages as $key => $value) {
                            $messages[$index . "." . $key] = $value;
                        }
                    }
                }
            }
        }
        $validator = Validator::make($this->values, $rules, $messages, $attributes);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return [
                "success" => false,
                "errors" => $errors
            ];
        }
        return ["success" => true];
    }

    public function setLoading()
    {
        $this->loading = false;
    }

    private function makeDataValues()
    {
        $panels = $this->module->getVisibleFieldPanels('', $this->entity, $this->crudType);
        foreach ($panels as $panel) {
            foreach ($panel->fields as $field) {
                if ($field->type !== FIELD_TYPES::MODULE->value) {
                    if (in_array($field->type, [FIELD_TYPES::SELECT->value, FIELD_TYPES::UPLOAD->value])) {
                        $value = data_get($this->entity, $field->field, []);
                        $this->values[$field->field] = is_array($value) ? $value : [$value];
                    } else {
                        $this->values[$field->field] = data_get($this->entity, $field->field);
                    }
                }
            }
        }
    }

    public function mount()
    {
        $this->makeApplication();
        if ($this->entity) {
            $this->makeDataValues();
            $this->dispatch("crud:setValues-" . $this->crudId, $this->values);
        }
    }

    public function render()
    {
        $this->dispatch("crud:setFieldsLoading-" . $this->crudId, $this->loading);
        return view('supernova-livewire-views::crud.header');
    }
}
