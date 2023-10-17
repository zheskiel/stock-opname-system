<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class Manager extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;
    use Notifiable;
    use HasRoles;

    protected $guard_name = "manager-api";
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
        'api_token',
    ];

    public function outlets()
    {
        return $this->belongsToMany('App\Models\Outlet', 'manager_outlet_supervisor')->withPivot('supervisor_id');
    }

    public function supervisor()
    {
        return $this->belongsToMany('App\Models\Supervisor', 'manager_outlet_supervisor')->withPivot('outlet_id');
    }

    public function staff()
    {
        return $this->hasMany('App\Models\Staff', 'outlet_id', 'id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
