<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = "outlet";

    protected $fillable = [
        'name',
        'slug',
        'manager_id',
        'location_id',
    ];

    protected $with = [
        // 'manager',
        // 'supervisor',
    ];

    protected $hidden = [
        // 'id',
        'manager_id',
        'location_id',
        'created_at',
        'updated_at',
    ];

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'manager_id');
    }

    public function supervisor()
    {
        return $this->belongsToMany('App\Models\Supervisor', 'manager_outlet_supervisor');
    }
}
