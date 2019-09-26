<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $table = 'tasks';
    protected $primarykey = 'id'; 
    protected $fillable = ['name','description','status','deleted','coordinators_id'];

    public function Coordinators()
    {
    	return $this->belongsTo('App\Coordinators')->where('deleted', 0);
    }

    public function Tasks_contents()
    {
        return $this->hasMany('App\Tasks_contents')->with('Tcxts','Teachers','Teaching_periods')->where('deleted',0);
    }


}
