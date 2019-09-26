<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suxts extends Model
{
    protected $table = 'suxts';
    protected $primarykey = 'id'; 
    protected $fillable = ['status','deleted','subjects_id','teachers_id'];

    public function Subjects()
    {
    	return $this->belongsTo('App\Subjects')->select(['id','name'])->where('deleted', 0);
    }

    public function Teachers()
    {
    	return $this->belongsTo('App\Teachers')->select(['id', 'users_id'])->with('Users')->where('deleted', 0);
    }
}
