<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class School_years extends Model
{
    protected $table = 'school_years';
    protected $primarykey = 'id'; 
    protected $fillable = ['name','description','status','deleted','admins_id'];

    public function Admins()
    {
        return $this->belongsTo('App\Admins')->with('Users')->where('deleted', 0);
    }

    public function Teaching_periods()
    {
        return $this->hasMany('App\Teaching_periods')->select(['id', 'name', 'description', 'school_years_id'])->with('Tasks_contents')->where('deleted', 0);
    }

    public function Periods(){
        return $this->hasMany('App\Teaching_periods')->where('deleted', 0);
    }
}
