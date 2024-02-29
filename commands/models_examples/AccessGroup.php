<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessGroup extends Model
{
    protected $table = "access_groups";
    public $guarded = ["created_at"];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, "access_group_permissions", "access_group_id", "permission_id");
    }
}
