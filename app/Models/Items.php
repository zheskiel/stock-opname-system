<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $table = "items";

    protected $fillable = [
        'forms_id',
        'product_id',
        'product_code',
        'product_name',
        'unit',
        'unit_value',
        'unit_sku',
        'value'
    ];

    protected $with = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    public function form()
    {
        return $this->belongsTo('App\Models\Forms');
    }
}
