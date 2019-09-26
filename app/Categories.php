<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';
    protected $primarykey = 'id'; 
    protected $fillable = ['name','description','status','deleted','coordinators_id'];

    public function Coordinators()
    {
    	return $this->belongsTo('App\Coordinators')->where('deleted', 0);
    }
}
