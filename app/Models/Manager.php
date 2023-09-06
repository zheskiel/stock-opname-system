<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $table = "manager";

    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'outlet_id'
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'outlet_id',
        'created_at',
        'updated_at',
    ];

    public function supervisor()
    {
        return $this->hasMany('App\Models\Supervisor', 'outlet_id', 'id')->with('type');
    }

    public function staff()
    {
        return $this->hasMany('App\Models\Staff', 'outlet_id', 'id');
    }
}
