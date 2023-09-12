<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = "location";

    protected $fillable = [
        'name',
        'slug',
        'alias',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'district_id',
        'created_at',
        'updated_at',
    ];

    public function outlet()
    {
        return $this->hasMany('App\Models\Outlet', 'location_id', 'id');
    }
}
