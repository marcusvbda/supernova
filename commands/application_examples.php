<?php

namespace App\Http\Supernova;

use App\Http\Supernova\Modules\AccessGroups;
use App\Http\Supernova\Modules\Permissions;
use App\Http\Supernova\Modules\Users;
use marcusvbda\supernova\Application as SupernovaApplication;

class Application extends SupernovaApplication
{
    public function modules(): array
    {
        return [
            // times
            Users::class,

            //configurações
            AccessGroups::class,
            Permissions::class,
        ];
    }
}
