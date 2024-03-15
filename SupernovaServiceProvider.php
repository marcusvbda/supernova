<?php

namespace marcusvbda\supernova;

use App\Http\Supernova\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Livewire\Livewire;
use marcusvbda\supernova\commands\InstallCommand;

class SupernovaServiceProvider extends ServiceProvider
{
    private $novaApp;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->createApplicationFileIfNotExists();
        $this->novaApp = new Application();
    }

    public function createApplicationFileIfNotExists()
    {
        $path = app_path('Http/Supernova/Application.php');
        if (file_exists($path)) return;
        $content = file_get_contents(__DIR__ . "/commands/application_examples.php");
        if (!file_exists(app_path('Http/Supernova'))) mkdir(app_path('Http/Supernova'), 0777, true);
        file_put_contents(app_path('Http/Supernova/Application.php'), $content);
    }

    public function boot(Router $router): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views/', 'supernova');
        $this->loadViewsFrom(__DIR__ . '/livewire/views/', 'supernova-livewire-views');
        $this->publishes([
            'config.php' => config_path() . "/supernova.php",
        ]);
        $router->aliasMiddleware('supernova-default-middleware',  fn ($request, $next) => $this->novaApp->middleware($request, $next));
    }

    public function register(): void
    {
        $this->registerLivewireComponents();
    }

    protected function registerLivewireComponents()
    {
        Livewire::component('supernova::navbar', $this->novaApp->navbar());
        Livewire::component('supernova::datatable', $this->novaApp->datatable());
        Livewire::component('supernova::datatable-global-filter', $this->novaApp->datatableGlobalFilter());
        Livewire::component('supernova::datatable-header', $this->novaApp->datatableHeader());
        Livewire::component('supernova::datatable-header-filter', $this->novaApp->datatableHeaderFilter());
        Livewire::component('supernova::datatable-body', $this->novaApp->datatableBody());
        Livewire::component('supernova::datatable-pagination', $this->novaApp->datatablePagination());
        Livewire::component('supernova::details', $this->novaApp->details());
        Livewire::component('supernova::select-field', $this->novaApp->selectField());
        Livewire::component('supernova::login', $this->novaApp->loginForm());
        Livewire::component('supernova::breadcrumb', $this->novaApp->breadcrumb());
        Livewire::component('supernova::counter-card', $this->novaApp->counterCard());
        Livewire::component('supernova::dashboard', $this->novaApp->dashboard());
        Livewire::component('supernova::alerts', $this->novaApp->alerts());
        Livewire::component('supernova::crud', $this->novaApp->crud());

        $this->app->bind('supernova:install', InstallCommand::class);
        $this->commands([
            'supernova:install',
        ]);
    }
}
