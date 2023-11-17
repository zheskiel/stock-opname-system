<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Templates extends Model
{
    protected $table = "templates";

    protected $fillable = [
        'title',
        'slug',
        'supervisor_id',
        'supervisor_duty',
        'outlet_id',
        'manager_id',
        'owned',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [];

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'manager_id');
    }

    public function supervisor()
    {
        return $this->belongsTo('App\Models\Supervisor', 'supervisor_id');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Models\Outlet', 'outlet_id');
    }

    public function details()
    {
        return $this->belongsToMany('App\Models\Details', 'details_templates');
    }
}
