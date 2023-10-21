<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    protected $table = "reports";

    protected $fillable = [
        'date',
        'additional',
        'waste',
        'damage',
        'notes',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
}
