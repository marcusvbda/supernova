<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $guarded = ['created_at'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public  $casts = [
        "avatar" => "array",
        "dark_mode" => "boolean",
    ];

    public function getAvatarImageAttribute()
    {
        return data_get($this->avatar, "0.url", "https://images.squarespace-cdn.com/content/v1/61252ad026b2035cd08c26a6/1658329270544-UI18QS4NLSP83HOA0WUB/user-placeholder-avatar.png?format=2500w");
    }

    public function getFirstNameAttribute()
    {
        return explode(" ", $this->name)[0];
    }

    public function access_group()
    {
        return $this->belongsTo(AccessGroup::class);
    }
}
