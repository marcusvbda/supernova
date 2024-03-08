<?php

namespace marcusvbda\supernova\livewire\components;

use App\Http\Supernova\Application;
use Livewire\Component;

class Breadcrumb extends Component
{
    public $items = [];
    public $entityUrl = null;
    public $entityId = null;
    public $parentId = null;
    public $parentModule = null;
    public $moduleId = null;

    public function mount()
    {
        $application = app()->make(config("supernova.application", Application::class));
        $route = request()->route();
        $currentRoute = $route->getName();
        array_unshift($this->items, [
            "title" => $application->homeTitle(),
            "route" => route("supernova.home")
        ]);
        if ($currentRoute != "supernova.home" && $this->moduleId) {
            $module = $application->getModule($this->moduleId, false);
            if ($currentRoute === "supernova.modules.index") {
                $this->items[] = [
                    "title" => $module->name()[1],
                    "route" => route("supernova.modules.index", ["module" => $this->moduleId]),
                ];
            }
            if ($currentRoute === "supernova.modules.details") {
                $this->items[] = [
                    "title" => $module->name()[1],
                    "route" => route("supernova.modules.index", ["module" => $this->moduleId]),
                ];
                $this->items[] = [
                    "title" => $module->name()[0] . " #" . $this->entityId,
                    "route" => $this->entityUrl
                ];
            }
            if ($currentRoute === "supernova.modules.create") {
                $this->items[] = [
                    "title" => $module->name()[1],
                    "route" => route("supernova.modules.index", ["module" => $this->moduleId]),
                ];
                $this->items[] = [
                    "title" => $module->title("create"),
                    "route" => route("supernova.modules.create", ["module" => $this->moduleId]),
                ];
            }
            if ($currentRoute === "supernova.modules.edit") {
                $this->items[] = [
                    "title" => $module->name()[1],
                    "route" => route("supernova.modules.index", ["module" => $this->moduleId]),
                ];
                $this->items[] = [
                    "title" => $module->name()[0] . " #" . $this->entityId,
                    "route" => route("supernova.modules.details", ["module" => $this->moduleId, 'id' => $this->entityId]),
                ];
                $this->items[] = [
                    "title" => $module->title("edit"),
                    "route" => route("supernova.modules.edit", ["module" => $this->moduleId, 'id' => $this->entityId]),
                ];
            }
            if ($currentRoute === "supernova.modules.field-create") {
                $pModule = $application->getModule($this->parentModule, false);
                $this->items[] = [
                    "title" => $module->name()[1],
                    "route" => route("supernova.modules.index", ["module" => $this->parentModule]),
                ];
                $this->items[] = [
                    "title" => $pModule->name()[0] . " #" . $this->parentId,
                    "route" => route("supernova.modules.details", ["module" => $this->parentModule, 'id' => $this->parentId]),
                ];
                $this->items[] = [
                    "title" => $module->title("create"),
                    "route" => route("supernova.modules.create", ["module" => $this->moduleId]),
                ];
            }
            if ($currentRoute === "supernova.modules.field-details") {
                $pModule = $application->getModule($this->parentModule, false);
                $this->items[] = [
                    "title" => $module->name()[1],
                    "route" => route("supernova.modules.index", ["module" => $this->parentModule]),
                ];
                $this->items[] = [
                    "title" => $pModule->name()[0] . " #" . $this->parentId,
                    "route" => route("supernova.modules.details", ["module" => $this->parentModule, 'id' => $this->parentId]),
                ];
                $this->items[] = [
                    "title" => $module->name()[0] . " #" . $this->entityId,
                    "route" => $this->entityUrl
                ];
            }
            if ($currentRoute === "supernova.modules.field-edit") {
                $pModule = $application->getModule($this->parentModule, false);
                $this->items[] = [
                    "title" => $module->name()[1],
                    "route" => route("supernova.modules.index", ["module" => $this->parentModule]),
                ];
                $this->items[] = [
                    "title" => $pModule->name()[0] . " #" . $this->parentId,
                    "route" => route("supernova.modules.details", ["module" => $this->parentModule, 'id' => $this->parentId]),
                ];
                $this->items[] = [
                    "title" => $module->name()[0] . " #" . $this->entityId,
                    "route" => $this->entityUrl
                ];
                $this->items[] = [
                    "title" => $module->title("edit"),
                    "route" => route("supernova.modules.edit", ["module" => $this->moduleId, 'id' => $this->entityId]),
                ];
            }
        }
    }

    public function render()
    {
        return view('supernova-livewire-views::breadcrumb');
    }
}
