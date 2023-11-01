<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    protected $table = "notes";

    protected $fillable = [
        'forms_id',
        'staff_id',
        'date',
        'notes',
    ];

    protected $with = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    public function form()
    {
        return $this->belongsTo('App\Models\Forms', 'forms_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff', 'staff_id');
    }
}
