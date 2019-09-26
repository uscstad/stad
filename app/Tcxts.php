<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tcxts extends Model
{
    protected $table = 'tcxts';
    protected $primarykey = 'id'; 
    protected $fillable = ['status','deleted','tasks_contents_id','teachers_id'];

    public function Tasks_contents()
    {
    	return $this->belongsTo('App\Tasks_contents')->with('Tasks')->where('deleted', 0);
    }

    public function Teachers()
    {
    	return $this->belongsTo('App\Teachers')->select(['id', 'users_id'])->with('Users')->where('deleted', 0);
    }
}
