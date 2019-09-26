<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasks_contents extends Model
{
    protected $table = 'tasks_contents';
    protected $primarykey = 'id'; 
    protected $fillable = ['type','categories','start_date','final_date','attachments','comments','detail','answer','archiveName','positive','level_educations','subjects', 'answer', 'archiveName', '´positive','status','deleted','teachers_id','teaching_periods_id','tasks_id'];

    public function Teachers()
    {
    	return $this->belongsTo('App\Teachers')->select(['id', 'profession', 'specialty', 'users_id'])->with('Users')->where('deleted', 0);
    }

    public function Teaching_periods()
    {
    	return $this->belongsTo('App\Teaching_periods')->select(['id', 'name', 'description', 'school_years_id'])->where('deleted', 0);
    }

    public function Tasks()
    {
    	return $this->belongsTo('App\Tasks')->select(['id', 'name', 'description','coordinators_id'])->where('deleted', 0);
    }

    public function Tcxts()
    {
        return $this->hasMany('App\Tcxts')->select(['id', 'tasks_contents_id', 'teachers_id'])->with('Teachers')->where('deleted',0);
    }

    public function Messages_contents(){
        return $this->hasMany('App\Messages_contents')->with('Users')->where('deleted', 0);
    }

    public function Prologues(){
        return $this->hasOne('App\Prologues')->where('status','processing');
    }
}
