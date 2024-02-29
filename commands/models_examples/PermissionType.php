<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionType extends Model
{
    protected $table = "permission_types";
    public $guarded = ["created_at"];
}
