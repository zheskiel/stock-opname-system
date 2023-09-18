<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Permission\Traits\HasRoles;

class Manager extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $guard_name = "web";
    protected $table = "manager";

    protected $fillable = [
        'name',
        'slug',
        'email',
        'password'
    ];

    protected $with = [
        // 'supervisor'
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    public function outlets()
    {
        return $this->belongsToMany('App\Models\Outlet');
    }

    public function supervisor()
    {
        return $this->belongsToMany('App\Models\Supervisor', 'manager_outlet_supervisor');
    }

    public function staff()
    {
        return $this->hasMany('App\Models\Staff', 'outlet_id', 'id');
    }
}
