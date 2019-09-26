<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tasks_contents;

class TasksIndividualsController extends Controller
{
    public function store(Request $request){
        $this->validate($request, [
        	'id'   				=> 'required',
            'positive'    		=> 'required',
            'recommendations'	=> 'required',
        ]);
        //
        $tasks = Tasks_contents::with('Prologues')->find($request->id);
        if($request->positive == 1){
        	$tasks->positive		= "yes";
        	$tasks->recommendations = $request->recommendations;
        	$tasks->final_date 		= $tasks->Prologues->final_date;
            //
            $tasks->Prologues->accepted = "yes";
            $tasks->Prologues->update();
        } else {
        	$tasks->positive		= "no";
        	$tasks->recommendations = $request->recommendations;
            //
            $tasks->Prologues->accepted = "no";
            $tasks->Prologues->update();
        }
        $tasks->update();
        return;
    }
}
