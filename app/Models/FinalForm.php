<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalForm extends Model
{
    protected $table = "final_form";

    protected $fillable = [
        'product_code',
        'product_name',
        'unit_sku',
        'calculated',
        'items'
    ];

    protected $with = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
