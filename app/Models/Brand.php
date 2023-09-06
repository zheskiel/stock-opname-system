<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = "brand";

    protected $fillable = [
        'name',
        'slug',
        'admin_id'
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function province()
    {
        return $this->hasMany('App\Models\Province')->with('regency');
    }
}
