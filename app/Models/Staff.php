<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = "staff";

    protected $fillable = [
        'name',
        'slug',
        'supervisor_id'
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'outlet_id',
        'manager_id',
        'staff_type_id',
        'supervisor_id',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'manager_id')->with('supervisor');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Models\Outlet', 'outlet_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff', 'staff_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\StaffType', 'staff_type_id');
    }
}
