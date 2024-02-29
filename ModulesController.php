<?php

namespace marcusvbda\supernova;

use App\Http\Controllers\Controller;
use App\Http\Supernova\Application;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ModulesController extends Controller
{
    private $application;
    public  function __construct()
    {
        $this->application = app()->make(config("supernova.application", Application::class));
    }

    public function edit($module, $id)
    {
        $module = $this->application->getModule($module);
        if (!$module->canEdit()) abort(403);
        $target = $module->makeModel()->findOrFail($id);
        return $module->edit($target);
    }

    public function create($module): View
    {
        $module = $this->application->getModule($module);
        if (!$module->canCreate()) abort(403);
        return $module->create();
    }

    public function details($module, $id): View
    {
        $module = $this->application->getModule($module);
        if (!$module->canViewIndex()) abort(403);
        $target = $module->makeModel()->findOrFail($id);
        return $module->details($target);
    }

    public function index($module): View
    {
        $module = $this->application->getModule($module);
        if (!$module->canViewIndex()) abort(403);
        return $module->index();
    }

    public function dashboard(): View
    {
        $this->application = app()->make(config("supernova.application", Application::class));
        return view("supernova::dashboard");
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->back();
    }

    public function login()
    {
        if (Auth::check()) return redirect()->route("supernova.home");
        $redirect = request()->get("redirect") ?? "/";
        return view("supernova::auth.login", compact("redirect"));
    }

    public function fieldCreate($parentModule, $parentId, $fieldModule)
    {
        $module = $this->application->getModule($fieldModule, false);
        if (!$module->canCreate()) {
            abort(403);
        }
        $createView = $module->create();
        $createView->parent_module = $parentModule;
        $createView->parent_id = $parentId;
        return $createView;
    }

    public function fieldDetails($parentModule, $parentId, $fieldModule, $fieldModuleId)
    {
        $module = $this->application->getModule($fieldModule, false);
        if (!$module->canViewIndex()) {
            abort(403);
        }
        $target = $module->makeModel()->findOrFail($fieldModuleId);
        $detailsView = $module->details($target);
        $detailsView->parent_module = $parentModule;
        $detailsView->parent_id = $parentId;
        return $detailsView;
    }

    public function fieldEdit($parentModule, $parentId, $fieldModule, $fieldModuleId)
    {
        $module = $this->application->getModule($fieldModule, false);
        if (!$module->canEdit()) {
            abort(403);
        }
        $target = $module->makeModel()->findOrFail($fieldModuleId);
        $editView = $module->edit($target);
        $editView->parent_module = $parentModule;
        $editView->parent_id = $parentId;
        return $editView;
    }

    public function uploadDowload($disk, $file)
    {
        $splitted = explode(".", $file);
        $name = explode("-", $splitted[0]);
        $id = array_pop($name);
        $path = implode("/", $name);
        $extension = $splitted[1];
        $file = Storage::disk($disk)->get($path . "/" . $id);
        if (!$file) abort(404);
        return response($file)->header('Content-Type', 'image/' . $extension);
    }
}
