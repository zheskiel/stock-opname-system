<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Details extends Model
{
    protected $table = "details";

    protected $fillable = [
        'product_id',
        'product_code',
        'product_name',
        'unit_label',
        'unit_value',
        'receipt_tolerance',
    ];

    protected $hidden = [
        // 'id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [];
}
