<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tasks_contents;

class ResponsesController extends Controller
{
    public function store(Request $request){
        $this->validate($request, [
        	'id'   		=> 'required',
            'answer'    => 'required',
            'annexed'   => 'required',
            'comments'  => 'required',
        ]);
        //
        $tasks = Tasks_contents::find($request->id);
        if(!$tasks->archiveName){
            $base = explode(',', $request->get('annexed'));
            $file = $base[1];
            $name = explode('/', $base[0]);
            $file = str_replace(' ', '+', $file);
            $fileName = \Auth::user()->id.'_'.time().'_'.str_replace(' ', '_', $name[0]);
            \File::put(public_path(). '/attachments/' . $fileName, base64_decode($file));
            $annexed = 'attachments/' . $fileName;
            $tasks->archiveName = $annexed;
        }    
        //           
        $tasks->comments	= $request->comments;
        $tasks->answer 		= $request->answer;
        $tasks->status 		= 'pending';
        $tasks->update();

        return;
    }
}
