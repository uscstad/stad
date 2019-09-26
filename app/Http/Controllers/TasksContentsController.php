<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tasks;
use App\Tasks_contents;
use App\Tcxts;

class TasksContentsController extends Controller
{
    public function store(Request $request){
        $this->validate($request, [
            'id'                  	=> 'required',
            'type'					=> 'required',	
            'teachers_id'           => 'required',
            'start_date'            => 'required',
            'final_date'            => 'required',
            'teaching_periods_id'   => 'required',
        ]);
        //
        $teacher = (object) $request->get('teachers_id');
        $groups = $request->get('teachers_group');
        //
        $tasks = Tasks::find($request->get('id'));
        $tasks->type = $request->get('type');
        $tasks->update();
        //
        if(!$tasks->Tasks_contents){
            $tasks_content = Tasks_contents::create([
                'tags'                  => json_encode($request->get('tags')),
                'start_date'            => $request->get('start_date'),
                'final_date'            => $request->get('final_date'),
                'teachers_id'           => $teacher->id,
                'teaching_periods_id'   => $request->get('teaching_periods_id'),
                'tasks_id'              => $tasks->id,
                'attachments'           => $request->get('attachments'),
            ]);
        } else {
            $tasks->Tasks_contents->tags                = json_encode($request->get('tags'));
            $tasks->Tasks_contents->start_date          = $request->get('start_date');
            $tasks->Tasks_contents->final_date          = $request->get('final_date');
            $tasks->Tasks_contents->teachers_id         = $teacher->id;
            $tasks->Tasks_contents->teaching_periods_id = $request->get('teaching_periods_id');
            $tasks->Tasks_contents->attachments         = $request->get('attachments');
            $tasks->Tasks_contents->update();
        }
        
        unset($tasks);
        //
        if($groups != "No Data"){
            foreach ($groups as $value) {
                Tcxts::create([
                    'tasks_contents_id' => $tasks_content->id,
                    'teachers_id'       => $value['id'],
                ]);
            }
        }
        unset($tasks_content);
        return;
    }

}
