<?php
namespace marcusvbda\supernova\commands;

use Illuminate\Console\Command;

class CreateDatabase extends Command
{
    protected $signature = 'supernova:create-db {dbname} {connection?}';
    protected $settings = [];

    public function handle()
    {
     try{
         $dbname = $this->argument('dbname');
         $connection = $this->hasArgument('connection') && $this->argument('connection') ? $this->argument('connection'): DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);

         $hasDb = DB::connection($connection)->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "."'".$dbname."'");

         if(empty($hasDb)) {
             DB::connection($connection)->select('CREATE DATABASE '. $dbname);
             $this->info("Database '$dbname' created for '$connection' connection");
         }
         else {
             $this->info("Database $dbname already exists for $connection connection");
         }
     }
     catch (\Exception $e){
         $this->error($e->getMessage());
     }
   }
}
