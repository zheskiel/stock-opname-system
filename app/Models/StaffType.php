<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Staff;

class StaffType extends Model
{
    protected $table = "staff_type";

    protected $fillable = [
        'name',
        'slug',
        'supervisor_id',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'supervisor_id',
        'created_at',
        'updated_at',
    ];

    public function staffs()
    {
        return $this->belongsToMany(Staff::class, 'staff_supervisor_staff_type');
    }
}
