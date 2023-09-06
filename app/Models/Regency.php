<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    protected $table = "regency";

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'province_id',
        'created_at',
        'updated_at',
    ];

    public function district()
    {
        return $this->hasMany('App\Models\District', 'regency_id', 'id')->with('location');
    }
}
