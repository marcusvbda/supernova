<?php

namespace marcusvbda\supernova;

use App\Models\User;
use Auth;
use marcusvbda\supernova\livewire\components\Alerts;
use marcusvbda\supernova\livewire\components\Breadcrumb;
use marcusvbda\supernova\livewire\components\CounterCard;
use marcusvbda\supernova\livewire\components\CrudCustomComponent;
use marcusvbda\supernova\livewire\components\CrudHeader;
use marcusvbda\supernova\livewire\components\CrudTextField;
use marcusvbda\supernova\livewire\components\CrudSelectField;
use marcusvbda\supernova\livewire\components\CrudUploadField;
use marcusvbda\supernova\livewire\components\Dashboard;
use marcusvbda\supernova\livewire\components\DatatableBody;
use marcusvbda\supernova\livewire\components\DatatableGlobalFilter;
use marcusvbda\supernova\livewire\components\DatatableHeader;
use marcusvbda\supernova\livewire\components\DatatableHeaderFilter;
use marcusvbda\supernova\livewire\components\DatatablePagination;
use marcusvbda\supernova\livewire\components\Details;
use marcusvbda\supernova\livewire\components\Login;
use marcusvbda\supernova\livewire\components\Navbar;
use marcusvbda\supernova\livewire\components\SelectField;

class Application
{
    protected $modulesNamespace;
    protected $modulesPath;

    public function __construct()
    {
        $this->modulesNamespace = config("supernova.modules_namespace", "App\\Http\\Supernova\\Modules\\");
        $this->modulesPath = config("supernova.modules_path", "Http/Supernova/Modules/");
    }

    protected function isApi()
    {
        return request()->wantsJson();
    }

    public function homeTitle(): string
    {
        return "Dashboard";
    }

    public function middleware($request, $next)
    {
        if (Auth::check()) return $next($request);
        return redirect()->route('supernova.login', ["redirect" => request()->path()]);
    }

    public function darkMode(): bool
    {
        return config("supernova.default_theme", "light") === "dark";
    }

    public function menuUserNavbar(): array
    {
        $items = [];
        $items["Sair"] = route("supernova.logout");

        $user = Auth::user();
        return [
            "element" => <<<BLADE
                <div class="flex items-center gap-3">
                    <img class="h-8 w-8 rounded-full" src="$user->avatarImage">
                    <span class='dark:text-gray-200 font-medium'>$user->name</span>
                </div>
            BLADE,
            "items" => $items
        ];
    }

    public function logoHeigth(): string
    {
        return 100;
    }

    public function logo(): string
    {
        return "https://tailwindui.com/img/logos/mark.svg?color=blue&shade=500";
    }

    public function title(): string
    {
        return config("app.name");
    }

    public function styles(): string
    {
        return <<<CSS
            /* styles here ... */
        CSS;
    }

    public function icon(): string
    {
        return "/favicon.ico";
    }

    public function getModule($module, $checkDeclaration = true): Module
    {
        $module = str_replace("-", " ", $module);
        $module = str_replace(" ", "", ucwords($module));
        $module = $this->modulesNamespace . $module;
        if (!class_exists($module)) abort(404);
        if ($checkDeclaration) {
            $modules = $this->modules();
            if (!in_array($module, $modules)) abort(404);
        }
        return app()->make($module);
    }

    public function modules(): array
    {
        $path = $this->modulesPath;
        $namespace = $this->modulesNamespace;
        $modules = [];
        foreach (scandir(app_path($path)) as $item) {
            if ($item != "." && $item != "..") {
                $modules[] = $namespace . ucfirst(str_replace(".php", "", $item));
            }
        }

        return $modules;
    }

    public function getAllModules(): array
    {
        return array_map(fn ($module) => app()->make($module), $this->modules());
    }

    public function crud(): string
    {
        return Crud::class;
    }

    public function loginForm(): string
    {
        return Login::class;
    }

    public function navbar(): string
    {
        return Navbar::class;
    }

    public function crudSelectField(): string
    {
        return CrudSelectField::class;
    }

    public function crudUploadField(): string
    {
        return CrudUploadField::class;
    }

    public function crudTextField(): string
    {
        return CrudTextField::class;
    }

    public function datatableGlobalFilter(): string
    {
        return DatatableGlobalFilter::class;
    }

    public function datatableHeader(): string
    {
        return DatatableHeader::class;
    }

    public function datatableHeaderFilter(): string
    {
        return DatatableHeaderFilter::class;
    }

    public function datatableBody(): string
    {
        return DatatableBody::class;
    }

    public function crudCustomComponent(): string
    {
        return CrudCustomComponent::class;
    }

    public function datatablePagination(): string
    {
        return DatatablePagination::class;
    }

    public function details(): string
    {
        return Details::class;
    }

    public function alerts(): string
    {
        return Alerts::class;
    }

    public function UserModel(): string
    {
        return User::class;
    }

    public function Breadcrumb(): string
    {
        return Breadcrumb::class;
    }

    public function DashboardGreetingMessage()
    {
        $user = Auth::user();
        $hour = date('H');
        $sufix = ($user?->firstName ?? $user->name) . "!";
        if ($hour >= 5 && $hour <= 12) {
            return "Bom dia, $sufix";
        } else if ($hour > 12 && $hour <= 18) {
            return "Boa tarde, $sufix";
        } else {
            return "Boa noite, $sufix";
        }
    }

    public function dashboard(): string
    {
        return Dashboard::class;
    }

    public function counterCard(): string
    {
        return CounterCard::class;
    }

    public function crudHeader(): string
    {
        return CrudHeader::class;
    }

    public function dashboardMetrics()
    {
        $modules = $this->getAllModules();
        $cards = [];

        foreach ($modules as $module) {
            $moduleCards = $module->dashboardMetrics();
            $cards = array_merge($cards, $moduleCards);
        }

        return $cards;
    }

    public function dashboardContent()
    {
        $cards = $this->dashboardMetrics();
        return compact("cards");
    }

    public function cardCounterReloadTime(): int
    {
        return 60;
    }

    public function menuItems(): array
    {
        $modules =  $this->getAllModules();
        $items = [
            $this->homeTitle() => route("supernova.home")
        ];
        foreach ($modules as $module) {
            if (!$module->menu()) continue;
            $menu = $module->menu();
            if (!strpos($menu, ".")) {
                [$title, $url] =  $this->extractItemDetails($menu);
                $items[$title] = $url;
            } else {
                $menu = explode(".", $menu);
                [$title, $url] =  $this->extractItemDetails($menu[1]);
                $items[$menu[0]][$title] = $url;
            }
        }
        return $items;
    }

    public function extractItemDetails($item)
    {
        $url = str_replace("'", "", str_replace("href='", "", str_replace("}", "", explode("{", $item)[1])));
        $title =  substr($item, 0, strpos($item, "{"));
        return [$title, $url];
    }

    public function selectField()
    {
        return SelectField::class;
    }

    public static function message($type, $message)
    {
        session()->push('quick.alerts', (object) ["type" => $type, "message" => $message, "closeable" => true]);
    }
}
