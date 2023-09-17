<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = "template";

    protected $fillable = [
        'product_id',
        'product_code',
        'product_name',
        'unit_label',
        'unit_value',
        'receipt_tolerance',
        'supervisor_id',
        'outlet_id',
        'manager_id',
        'owned',
        'status'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [];

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'manager_id');
    }

    public function supervisor()
    {
        return $this->belongsTo('App\Models\Supervisor', 'supervisor_id');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Models\Outlet', 'outlet_id');
    }
}
