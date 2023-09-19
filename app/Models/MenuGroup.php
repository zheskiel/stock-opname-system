<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    protected $fillable = [
        'name',
        'status',
        'permission_name',
        'position'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }
}