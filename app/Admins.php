<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admins extends Model
{
    protected $table = 'admins';
    protected $primarykey = 'id'; 
    protected $fillable = ['status','deleted','users_id'];

    public function Users(){
    	return $this->belongsTo('App\User')->where('deleted', 0);
    }

    public function Teaching_periods(){
        return $this->hasMany('App\Teaching_periods')->where('deleted', 0);
    }

    public function Configs(){
        return $this->hasOne('App\Configs')->where('deleted',0);
    }

    public function Coordinators(){
        return $this->hasOne('App\Coordinators')->where('deleted',0);
    }

    public function Teachers(){
        return $this->hasOne('App\Teachers')->where('deleted',0);
    }
}
