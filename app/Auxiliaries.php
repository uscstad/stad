<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auxiliaries extends Model
{
    protected $table = 'auxiliaries';
    protected $primarykey = 'id'; 
    protected $fillable = ['specialty','profession','status','deleted', 'admins_id', 'users_id'];

    public function Users()
    {
    	return $this->belongsTo('App\User')->where('deleted', 0);
    }

    public function Admins()
    {
        return $this->hasMany('App\Admins')->where('deleted', 0);
    }
}
