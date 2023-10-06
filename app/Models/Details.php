<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Details extends Model
{
    protected $table = "details";

    protected $fillable = [
        "templates_id",
        'product_id',
        'product_code',
        'product_name',
        'receipt_tolerance',
        'units',
    ];

    protected $hidden = [
        // 'id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [];

    public function template()
    {
        return $this->belongsTo('App\Models\Templates', 'templates_id');
    }
}
