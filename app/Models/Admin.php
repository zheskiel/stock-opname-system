<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;

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
