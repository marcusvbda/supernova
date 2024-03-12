<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;

class Details extends Component
{
    public $module;
    public $entity;
    public $panels = [];
    public $canDelete = false;
    public $canEdit = false;
    public $parentId = null;
    public $parentModule = null;

    public function placeholder()
    {
        return view('supernova-livewire-views::skeleton', ['size' => '500px']);
    }

    private function getModule()
    {
        $application = app()->make(config('supernova.application', Application::class));
        return $application->getModule($this->module, false);
    }

    public function redirectToEdit()
    {
        $module = $this->getModule();
        if ($this->parentId && $this->parentModule) {
            return redirect()->route('supernova.modules.field-edit', [
                'module' => $this->parentModule, 'id' => $this->parentId,
                'field' => $module->id(), 'fieldId' =>  $this->entity->id
            ]);
        }
        return redirect()->route('supernova.modules.edit', ['module' => $module->id(), 'id' => $this->entity->id]);
    }

    public function deleteEntity()
    {
        $module = $this->getModule();
        $module->onDelete($this->entity);
        $application = app()->make(config('supernova.application', Application::class));
        $application::message("success", "Registro deletado com sucesso");
        if ($this->parentId && $this->parentModule) {
            return redirect()->route('supernova.modules.details', [
                'module' => $this->parentModule, 'id' => $this->parentId
            ]);
        }
        return redirect()->route('supernova.modules.index', ['module' => $module->id()]);
    }

    public function render()
    {
        return view('supernova-livewire-views::details.index');
    }
}
