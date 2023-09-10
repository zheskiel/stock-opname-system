<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffType extends Model
{
    protected $table = "staff_type";

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'supervisor_id',
        'created_at',
        'updated_at',
    ];

    public function staff()
    {
        return $this->hasMany('App\Models\Staff', 'staff_type_id', 'id');
    }
}
