<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorType extends Model
{
    protected $table = "supervisor_type";

    protected $fillable = [
        'name',
        'slug',
        'duty'
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function staffs()
    {
        return $this->hasMany('App\Models\Staffs');
    }
}
