<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $table = "supervisor";

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'outlet_id',
        'manager_id',
        'created_at',
        'updated_at',
    ];

    public function type()
    {
        return $this->hasMany('App\Models\StaffType', 'supervisor_id', 'id')->with('staff');
    }
}
