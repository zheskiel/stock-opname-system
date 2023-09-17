<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use Notifiable;

    protected $table = "staff";

    protected $fillable = [
        'name',
        'slug',
        'email',
        'sv_type_label',
        'outlet_id',
        'manager_id',
        'staff_type_id',
        'supervisor_id',
        'is_supervisor',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected $with = [];

    protected $hidden = [
        'id',
        'outlet_id',
        'manager_id',
        'staff_type_id',
        'supervisor_id',
        'is_supervisor',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'pivot'
    ];

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'manager_id')->with('supervisor');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Models\Outlet', 'outlet_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\StaffType', 'staff_type_id');
    }
}
