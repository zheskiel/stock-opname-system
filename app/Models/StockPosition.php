<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPosition extends Model
{
    protected $table = "stock_position";

    protected $fillable = [
        'date',
        'product_name',
        'product_code',
        'unit',
        'category',
        'subcategory',
        'value',
    ];

    protected $with = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
