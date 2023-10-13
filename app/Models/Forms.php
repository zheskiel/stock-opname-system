<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forms extends Model
{
    protected $table = "forms";

    protected $fillable = [
        'template_id',
        'manager_id',
        'outlet_id',
        'supervisor_id',
        'staff_id',
    ];

    protected $with = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function template()
    {
        return $this->belongsTo('App\Models\Templates');
    }

    public function items()
    {
        return $this->belongsToMany('App\Models\Items');
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Models\Outlet');
    }

    public function supervisor()
    {
        return $this->belongsTo('App\Models\Supervisor');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
}
