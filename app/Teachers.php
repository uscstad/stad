<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{
    protected $table = 'teachers';
    protected $primarykey = 'id'; 
    protected $fillable = ['specialty','profession','status','deleted', 'admins_id', 'users_id'];

    public function Users()
    {
    	return $this->belongsTo('App\User')->select(['id','type_doc','doc','name','lastname','user','email','address','mobile','phone','status','type','created_at','updated_at'])->where('deleted', 0);
    }

    public function Admins(){
        return $this->belongsTo('App\Admins')->where('deleted', 0);
    }

    public function Tasks_contents(){
        return $this->hasOne('App\Tasks_contents')->where('deleted', 0);
    }

    public function Person(){
        return $this->belongsTo('App\User')->select(['id','name','lastname'])->where('deleted', 0);
    }

    public function Lexts()
    {
        return $this->hasMany('App\Lexts')->select(['id','level_educations_id','teachers_id'])->with('Level_educations')->where('deleted',0);
    }

    public function Suxts()
    {
        return $this->hasMany('App\Suxts')->select(['id','subjects_id','teachers_id'])->with('Subjects')->where('deleted',0);
    }
}
