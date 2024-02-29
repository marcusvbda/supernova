<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use marcusvbda\supernova\FIELD_TYPES;
use Livewire\Attributes\Lazy;
use Livewire\WithFileUploads;

#[Lazy]
class Crud extends Component
{
    use WithFileUploads;
    public $module;
    public $entity;
    public $panels = [];
    public $values = [];
    public $uploadingValues = [];
    public $uploadValues = [];
    public $options = [];
    public $loaded_options = [];
    public $panelFallback = 'Cadastro de';
    public $crudType = 'create';
    public $parentId = null;
    public $parentModule = null;

    public function placeholder()
    {
        return view('supernova-livewire-views::skeleton', ['size' => '500px']);
    }

    public function mount()
    {
        if ($this->crudType === 'edit') {
            $this->values["id"] = data_get($this->entity, "id");
            $fields = $this->allFields();
            foreach ($fields as $field) {
                if ($field->type === FIELD_TYPES::SELECT->value) {
                    if ($field->multiple) {
                        $value = data_get($this->entity, $field->field);
                        $this->values[$field->field] = $value ? $value->pluck("id")->toArray() : [];
                    } else {
                        $value = data_get($this->entity, $field->field);
                        $this->values[$field->field] = $value ? [$value] : [];
                    }
                } elseif ($field->type === FIELD_TYPES::UPLOAD->value) {
                    $this->uploadValues[$field->field] = data_get($this->entity, $field->field, []) ?? [];
                } else {
                    $this->values[$field->field] = data_get($this->entity, $field->field);
                }
            }
        }
    }

    public function allFields()
    {
        $module = $this->getModule();
        $fields = $module->fields($this->entity, $this->crudType);
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

    public function rules()
    {
        request()->merge(["values" => $this->values]);
        $fields = $this->allFields();
        $rules = [];
        foreach ($fields as $field) {
            if ($field->rules) {
                $rules["values." . $field->field] = $field->rules;
            } else {
                $rules["values." . $field->field] = [];
            }
        }
        return $rules ?? [];
    }

    public function messages()
    {
        $fields = $this->allFields();
        $messages = [];
        foreach ($fields as $field) {
            if ($field->messages && count($field->messages)) {
                $index = "values." . $field->field;
                foreach ($field->messages as $key => $value) {
                    $messages[$index . "." . $key] = $value;
                }
            }
        }
        return $messages;
    }

    public function validationAttributes()
    {
        $fields = $this->allFields();
        $attr = [];
        foreach ($fields as $field) {
            if ($field->rules && $field->type !== FIELD_TYPES::UPLOAD->value) {
                $attr["values." . $field->field] = $field->field;
            }
        }
        return $attr;
    }

    public function updated($field)
    {
        $this->validateOnly($field);
        $fields = $this->allFields();
        $uploadFields = collect($fields)->filter(fn ($f) => $f->type === FIELD_TYPES::UPLOAD->value);
        foreach ($uploadFields as $uploadField) {
            if ($field === "uploadingValues" . "." . $uploadField->field && $this->uploadingValues[$uploadField->field]) {
                $this->validate(["uploadingValues." . $uploadField->field => $uploadField->rules], []);
                $file = $this->uploadingValues[$uploadField->field];
                $this->uploadingValues[$uploadField->field] = null;
                $this->uploadValues[$uploadField->field][] = $file;
            }
        }
    }

    private function getModule()
    {
        $application = app()->make(config('supernova.application', Application::class));
        return $application->getModule($this->module, false);
    }

    public function loadInputOptions($field)
    {
        $fields = $this->allFields();
        $field = collect($fields)->first(function ($f) use ($field) {
            return $f->field == $field;
        });
        $options_callback = $field->options_callback;
        if ($options_callback && is_callable($options_callback)) {
            $this->options[$field->field] = $options_callback();
        } else {
            $this->options[$field->field] = $field->options;
        }
        $this->loaded_options[$field->field] = true;
    }

    public function removeOption($field, $index)
    {
        $oldValues = data_get($this->values, $field, []);
        $newValues = collect($oldValues)->filter(fn ($item) => $item != $index);
        $this->values[$field] = $newValues->count() > 0 ? $newValues->toArray() : [];
    }

    public function setSelectOption($val, $field)
    {
        $this->values[$field][] = $val;
    }

    public function removeUploadValue($field, $index)
    {
        $oldValues = data_get($this->uploadValues, $field, []);
        $newValues = collect($oldValues)->filter(fn ($item, $row) => $row != $index);
        $this->uploadValues[$field] = $newValues->count() > 0 ? $newValues->toArray() : null;
    }

    public function save()
    {
        $this->validate($this->rules());
        $isField = $this->parentId && $this->parentModule;
        $module = $this->getModule();
        $values = ['save' => [], 'post_save' => [], 'uploads' => $this->uploadValues];
        $panels = $module->getVisibleFieldPanels('', $this->entity, $this->crudType);
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
                        if (isset($this->values[$field->field])) {
                            $values['save'][$field->field] = $this->values[$field->field];
                        }
                    }
                }
            }
        }
        $id = $module->onSave(data_get($this->entity, 'id'), $values, ['type' => $this->crudType, 'parent_id' => $this->parentId, 'parent_module' => $this->parentModule]);
        $application = app()->make(config('supernova.application', Application::class));
        $application::message("success", "Registro salvo com sucesso");
        if ($isField) {
            return redirect()->route('supernova.modules.details', ['module' => $this->parentModule, 'id' => $this->parentId]);
        }
        return redirect()->route('supernova.modules.details', ['module' => $module->id(), 'id' => $id]);
    }

    public function render()
    {
        return view('supernova-livewire-views::crud.index');
    }
}
