<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages_contents extends Model
{
    protected $table = 'messages_contents';
    protected $primarykey = 'id'; 
    protected $fillable = ['body','senders','status','deleted','tasks_contents_id','users_id'];

    public function Tasks_contents()
    {
    	return $this->belongsTo('App\Tasks_contents')->with('Tasks', 'Teachers')->select('id', 'start_date', 'final_date', 'teachers_id', 'tasks_id')->where('deleted', 0);
    }

    public function Users()
    {
        return $this->belongsTo('App\User')->select('id','name','lastname')->where('deleted', 0);
    }

    public function Prologues()
    {
        return $this->hasOne('App\Prologues')->where('deleted',0);
    }
}
