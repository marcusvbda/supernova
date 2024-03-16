<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class CrudUploadField extends Component
{
    use WithFileUploads;
    public $type;
    public $index;
    public $value = null;
    public $values = [];
    public $disabled = false;
    public $crudId;
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

    public function updatedValue($file)
    {
        $this->validateOnly('value');
        $this->value = null;
        $field = $this->module->getField($this->index);
        $disk = $field->uploadDisk;
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid();
        $file->storeAs($field->uploadPath, $fileName, $disk);
        $newValue = [
            "path" => $field->uploadPath,
            "id" => $fileName,
            "extension" => $extension,
            "disk" => $disk,
            "original_name" => $file->getClientOriginalName(),
            "size" => $file->getSize(),
        ];
        $this->values[] = $newValue;
        $this->dispatch('crud:updateField-' . $this->crudId, $this->index, $this->values);
    }

    public function removeFile($i)
    {
        $this->values = collect($this->values)->filter(fn ($item, $key) => $key != $i);
        $this->dispatch('crud:updateField-' . $this->crudId, $this->index, $this->values);
    }

    private function makeApplication()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        $this->module = $this->application->getModule($this->moduleId, false);
    }

    public function mount()
    {
        if ($this->entity) {
            $this->values = data_get($this->entity, $this->index);
        }
    }

    public function render()
    {
        return view('supernova-livewire-views::crud.live-fields.upload-field-component');
    }
}
