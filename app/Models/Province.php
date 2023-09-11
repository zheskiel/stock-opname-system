<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = "province";

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'brand_id',
        'created_at',
        'updated_at',
    ];

    public function regency()
    {
        return $this->hasMany('App\Models\Regency', 'province_id', 'id')->with('district');
    }
}
