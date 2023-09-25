<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $table = "supervisor";

    protected $fillable = [
        'name',
        'slug',
        'duty',
        'manager_id',
        'outlet_id',
        'is_supervisor',
        'supervisor_type_id'
    ];

    protected $with = [
        // 'supervisor_pic',
        // 'type'
    ];

    protected $hidden = [
        // 'id',
        // 'outlet_id',
        // 'manager_id',
        'supervisor_type_id',
        'staff_id',
        'created_at',
        'updated_at',
        'pivot'
    ];

    public function multiPivotType()
    {
        return $this->belongsToMany('App\Models\StaffType', 'staff_supervisor_staff_type');
    }

    public function type()
    {
        return $this->hasMany('App\Models\StaffType')->with('staffs');
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'manager_id');
    }

    public function supervisor_pic()
    {
        return $this->belongsTo('App\Models\Staff', 'staff_id');
    }

    public function staffs()
    {
        return $this->belongsToMany('App\Models\Staff', 'staff_supervisor_staff_type');
    }
}
