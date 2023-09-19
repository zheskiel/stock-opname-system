<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'route',
        'status',
        'permission_name',
        'menu_group_id',
        'position'
    ];

    protected $casts = ['status' => 'boolean'];
}