<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = "district";

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'regency_id',
        'created_at',
        'updated_at',
    ];

    public function location()
    {
        return $this->hasMany('App\Models\Location', 'district_id', 'id')->with('outlet');
    }
}
