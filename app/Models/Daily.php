<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Daily extends Model
{
    protected $table = "daily";

    protected $fillable = [
        'forms_id',
        'items_id',
        'items_code',
        'date',
        'value',
    ];

    protected $with = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function form()
    {
        return $this->belongsTo('App\Models\Forms', 'forms_id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Items', 'items_id');
    }
}
