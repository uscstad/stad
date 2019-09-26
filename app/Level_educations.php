<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level_educations extends Model
{
    protected $table = 'level_educations';
    protected $primarykey = 'id'; 
    protected $fillable = ['name','description','status','deleted','admins_id'];

    public function Admins()
    {
    	return $this->belongsTo('App\Admins')->where('deleted', 0);
    }

    public function Lexts()
    {
        return $this->hasMany('App\Lexts')->where('deleted',0);
    }
}
