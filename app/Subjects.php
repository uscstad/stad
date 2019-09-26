<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    protected $table = 'subjects';
    protected $primarykey = 'id'; 
    protected $fillable = ['name','description','status','deleted','admins_id'];

    public function Admins()
    {
        return $this->belongsTo('App\Admins')->where('deleted', 0);
    }

    public function Suxts()
    {
        return $this->hasMany('App\Suxts')->where('deleted',0);
    }
}
