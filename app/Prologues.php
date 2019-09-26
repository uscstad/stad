<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prologues extends Model
{
    protected $table = 'prologues';
    protected $primarykey = 'id'; 
    protected $fillable = ['start_date','final_date','justifications','archive','archiveName','status', 'accepted','tasks_contents_id'];

    public function Tasks_contents()
    {
    	return $this->belongsTo('App\Tasks_contents')->with('Tasks')->where('deleted', 0);
    }

}
