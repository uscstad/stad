<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_doc', 'doc', 'name', 'lastname', 'user', 'email', 'password', 'address', 'mobile', 'phone', 'status', 'deleted', 'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function Admins()
    {
        return $this->hasOne('App\Admins', 'users_id', 'id')->where('deleted',0);
    }

    public function Coordinators()
    {
        return $this->hasOne('App\Coordinators', 'users_id', 'id')->where('deleted',0);
    }

    public function Teachers()
    {
        return $this->hasOne('App\Teachers', 'users_id', 'id')->where('deleted',0);
    }

    public function Auxiliaries()
    {
        return $this->hasOne('App\Auxiliaries', 'users_id', 'id')->where('deleted',0);
    }
}
