<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $guard_name = "web";
    protected $table = "admin";

    protected $fillable = [
        'name',
        'email',
        'password',
        'brand_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [];

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id');
    }
}
