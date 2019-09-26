<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teaching_periods extends Model
{
    protected $table = 'teaching_periods';
    protected $primarykey = 'id'; 
    protected $fillable = ['name','description','start_date','final_date','status','deleted','school_years_id'];

    public function School_years()
    {
    	return $this->belongsTo('App\School_years')->select('name','id','admins_id')->where('deleted', 0);
    }

    public function Tasks_contents()
    {
        return $this->hasMany('App\Tasks_contents')->select(['id', 'styles', 'start_date', 'final_date', 'teaching_periods_id', 'tasks_id', 'teachers_id'])->with('Tasks','Teachers','Teaching_periods','Tcxts','Messages_contents')->where('deleted', 0);
    }

    public function countYears(){

        return $this->belongsTo('App\School_years')->where('admins_id', \Auth::user()->Admins->id)->where('deleted', 0);
    }
}
