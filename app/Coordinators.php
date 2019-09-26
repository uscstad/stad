<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coordinators extends Model
{
    protected $table = 'coordinators';
    protected $primarykey = 'id'; 
    protected $fillable = ['specialty','profession','status','deleted', 'admins_id', 'users_id'];

    public function Users()
    {
    	return $this->belongsTo('App\User')->where('deleted', 0);
    }

    public function Admins()
    {
        return $this->belongsTo('App\Admins')->where('deleted', 0);
    }
    
    public function Tasks(){
        return $this->hasMany('App\Tasks')->where('deleted', 0);
    }
}
