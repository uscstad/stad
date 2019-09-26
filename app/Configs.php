<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configs extends Model
{
    protected $table = 'configs';
    protected $primarykey = 'id'; 
    protected $fillable = ['status','deleted', 'admins_id', 'school_years_id', 'teaching_periods_id'];

    public function Admins()
    {
        return $this->belongsTo('App\Admins')->where('deleted', 0);
    }

    public function School_years()
    {
    	return $this->belongsTo('App\School_years')->select('name','id')->with('Teaching_periods')->where('deleted', 0);
    }

    public function Teaching_periods()
    {
    	return $this->belongsTo('App\Teaching_periods')->select(['id', 'name', 'description', 'school_years_id', 'start_date', 'final_date'])->where('deleted', 0);
        //return $this->belongsTo('App\Teaching_periods')->select(['id', 'name', 'description', 'school_years_id'])->with('Tasks_contents')->where('deleted', 0);
    }
}
