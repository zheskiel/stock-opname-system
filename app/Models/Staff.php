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

class Staff extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;
    use Notifiable;
    use HasRoles;

    protected $guard_name = "api";
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

    protected $with = [
        // 'type'
    ];

    protected $hidden = [
        // 'id',
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
