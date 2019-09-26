<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Prologues;
use App\Tasks_contents;

class ProloguesController extends Controller
{
    public function store(Request $request){
        //
        $tasks_content = Tasks_contents::find($request->id);
        //
        $this->validate($request, [
        	'id'   				=> 'required',
            'final_date'    	=> 'required',
            'new_final_date'  	=> 'required',
            'justifications'  	=> 'required',
            'evidence'			=> $tasks_content->attachments == 1 ? 'required' : '',
        ]);
        //
        $evidence = null;
        if($tasks_content->attachments == 'si' && $request->evidence != ''){
            $base = explode(',', $request->evidence);
            $file = $base[1];
            $name = explode('/', $base[0]);
            $file = str_replace(' ', '+', $file);
            $fileName = \Auth::user()->id.'_'.time().'_'.str_replace(' ', '_', $name[0]);
            \File::put(public_path(). '/justifications/' . $fileName, base64_decode($file));
            $evidence = 'justifications/' . $fileName;
        }
        
        $tasks_content->status = 'reprogrammed';
        $tasks_content->save();
        //
        $prologues = Prologues::create([
            'start_date'  		=> $request->final_date,
            'final_date'       	=> $request->new_final_date,
            'justifications'  	=> $request->justifications,
            'archive'  			=> $evidence ? 1 : 0,
            'archiveName'  		=> $evidence,
            'tasks_contents_id' => $request->id,
        ]);
        return;
    }
}
