<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = "permissions";
    public $guarded = ["created_at"];

    public function type()
    {
        return $this->belongsTo(PermissionType::class, "type_id");
    }
}
