<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lexts extends Model
{
    protected $table = 'lexts';
    protected $primarykey = 'id'; 
    protected $fillable = ['status','deleted','level_educations_id','teachers_id'];

    public function Level_educations()
    {
    	return $this->belongsTo('App\Level_educations')->select(['id','name'])->where('deleted', 0);
    }

    public function Teachers()
    {
    	return $this->belongsTo('App\Teachers')->select(['id', 'users_id'])->with('Users')->where('deleted', 0);
    }
}
