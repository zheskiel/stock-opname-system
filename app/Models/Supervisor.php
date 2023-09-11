<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $table = "supervisor";

    protected $fillable = [
        'name',
        'slug',
        'is_supervisor'
    ];

    protected $with = [
        'supervisor_pic'
    ];

    protected $hidden = [
        'id',
        'outlet_id',
        'manager_id',
        'supervisor_type_id',
        'staff_id',
        'created_at',
        'updated_at',
    ];



    public function type()
    {
        return $this->hasMany('App\Models\StaffType', 'supervisor_id', 'id')->with('staff');
    }

    public function supervisor_pic()
    {
        return $this->belongsTo('App\Models\Staff', 'staff_id');
    }

    public function staffs()
    {
        return $this->hasManyThrough('App\Models\Staff', 'App\Models\StaffType', 'id', 'staff_type_id');
    }
}
