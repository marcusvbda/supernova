<?php

namespace marcusvbda\supernova\commands;

use Illuminate\Console\Command;
use DB;

class InstallCommand extends Command
{
    protected $signature = 'supernova:install';

    protected $description = 'Install supernova package';

    protected $settings = [];

    public function makeEnvFile()
    {
        $name = $this->settings['name'] = $this->ask('What is your application name?', 'Supernova Application');
        $db_connection = $this->settings['db_connection'] = $this->ask('What is your database connection?', 'mysql');
        $db_host = $this->settings['db_host'] = $this->ask('What is your database host?', 'mysql');
        $db_port =  $this->settings['db_port'] = $this->ask('What is your database port?', '3306');
        $db_name = $this->settings['db_name'] = $this->ask('What is your database name?', 'db');
        $db_user = $this->settings['db_user'] = $this->ask('What is your database user?', 'root');
        $db_password = $this->settings['db_password'] = $this->ask('What is your database password?');
        $env = file_get_contents(__DIR__ . "/../.env_example");

        $env = str_replace("{{APP_NAME}}", $this->settings['name'], $env);
        $env = str_replace("{{DB_CONNECTION}}", $this->settings['db_connection'], $env);
        $env = str_replace("{{DB_HOST}}", $this->settings['db_host'], $env);
        $env = str_replace("{{DB_PORT}}", $this->settings['db_port'], $env);
        $env = str_replace("{{DB_DATABASE}}", $this->settings['db_name'], $env);
        $env = str_replace("{{DB_USERNAME}}", $this->settings['db_user'], $env);
        $env = str_replace("{{DB_PASSWORD}}", $this->settings['db_password'], $env);

        config(["database.connections.$db_connection.host" => $db_host]);
        config(["database.connections.$db_connection.port" => $db_port]);
        config(["database.connections.$db_connection.database" => $db_name]);
        config(["database.connections.$db_connection.username" => $db_user]);
        config(["database.connections.$db_connection.password" => $db_password]);
        config(["app.name" => $name]);
        DB::reconnect($db_connection);

        file_put_contents(base_path('.env'), $env);
        $this->info('Env file created successfully!');
        sleep(2);
    }

    private function createModels()
    {
        $this->info('Creating models...');
        sleep(2);
        $userModel = file_get_contents(__DIR__ . "/models_examples/User.php");
        $accessGroupModel = file_get_contents(__DIR__ . "/models_examples/AccessGroup.php");
        $permissionModel = file_get_contents(__DIR__ . "/models_examples/Permission.php");
        $permissionType = file_get_contents(__DIR__ . "/models_examples/PermissionType.php");
        file_put_contents(app_path('Models/User.php'), $userModel);
        file_put_contents(app_path('Models/AccessGroup.php'), $accessGroupModel);
        file_put_contents(app_path('Models/Permission.php'), $permissionModel);
        file_put_contents(app_path('Models/PermissionType.php'), $permissionType);
        $this->info('Models created successfully!');
    }

    private function createModules()
    {
        $this->info('Creating modules...');
        sleep(2);
        if (!file_exists(app_path('Http/Supernova/Modules'))) mkdir(app_path('Http/Supernova/Modules'), 0777, true);
        $usersModule = file_get_contents(__DIR__ . "/modules_examples/Users.php");
        $permissionsModule = file_get_contents(__DIR__ . "/modules_examples/Permissions.php");
        $accessGroupModule = file_get_contents(__DIR__ . "/modules_examples/AccessGroups.php");
        file_put_contents(app_path('Http/Supernova/Modules/Users.php'), $usersModule);
        file_put_contents(app_path('Http/Supernova/Modules/Permissions.php'), $permissionsModule);
        file_put_contents(app_path('Http/Supernova/Modules/AccessGroups.php'), $accessGroupModule);
        $this->info('Modules created successfully!');
    }

    public function handle()
    {
        $this->info('Installing supernova...');
        sleep(2);
        $this->makeEnvFile();
        $this->createModels();
        $this->createModules();
        $this->info('To finish run :');
        $this->info('php artisan migrate');
    }
}
