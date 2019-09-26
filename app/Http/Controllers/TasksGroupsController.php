<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tasks_contents;
use App\Prologues;

class TasksGroupsController extends Controller
{
    public function store(Request $request){
    	$this->validate($request, [
        	'id'   				=> 'required',
            'jobs'    			=> 'required',
            'jobs_comments'		=> 'required',
            'final_date'        => $request->correction == 1 ? 'required' : '',
            'new_final_date'	=> $request->correction == 1 ? 'required' : '',
            'justifications'    => $request->correction == 1 ? 'required' : '',
            'evidence'          => $request->correction == 1 ? 'required' : '',
        ]);
        //
        $tasks = Tasks_contents::with('Prologues')->find($request->id);
        $tasks->jobs 			= $request->jobs;
    	$tasks->jobs_comments 	= $request->jobs_comments;
        if($request->correction == 1){
            $evidence = null;
            if($request->evidence != ""){
                $base = explode(',', $request->evidence);
                $file = $base[1];
                $name = explode('/', $base[0]);
                $file = str_replace(' ', '+', $file);
                $fileName = \Auth::user()->id.'_'.time().'_'.$name[0];
                \File::put(public_path(). '/justifications/' . $fileName, base64_decode($file));
                $evidence = 'justifications/' . $fileName;
            }
            $prologues = Prologues::create([
                'start_date'        => $request->final_date,
                'final_date'        => $request->new_final_date,
                'justifications'    => $request->justifications,
                'archive'           => $evidence ? 1 : 0,
                'archiveName'       => $evidence,
                'tasks_contents_id' => $request->id,
            ]);
        	$tasks->status 		= 'reprogrammed';
        } elseif ($request->correction == 0 && $request->jobs == 0) {
            $tasks->status = 'unsatisfactory';    
        } else {
        	$tasks->status 		= 'finished';
        }
        $tasks->update();
    }
}
