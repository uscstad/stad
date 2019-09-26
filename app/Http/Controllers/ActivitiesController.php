<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tasks;

class ActivitiesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('activities.index');
    }

    public function list(){

        $tasks = Tasks::where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        //
        $data = [
            'pagination' => [
                'prev_page_url' => $tasks->previousPageUrl(),
                'next_page_url' => $tasks->nextPageUrl(),
                'total'         => $tasks->total(),
                'current_page'  => $tasks->currentPage(),
                'per_page'      => $tasks->perPage(),
                'last_page'     => $tasks->lastPage(),
                'from'          => $tasks->firstItem(),
                'to'            => $tasks->lastPage(),
            ],
            'activities'     	=> $tasks->all(),
        ]; 
        unset($tasks);
        return response()->json($data, 200);
    }

    public function getstatus(Request $request, $id){
        $tasks = tasks::find($id);
        $tasks->status = !$request->get('status');
        $tasks->update();
        return;
    }

    public function store(Request $request){
        $this->validate($request, [
            'name'                  => 'required',
        ]);
        //
        Tasks::create([
            'name'              => $request->get('name'),
            'description'       => $request->get('description') !=  "" ? $request->get('description') : "sin descripción.",
            'coordinators_id'   => \Auth::user()->Coordinators->id,
        ]);
        return;
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name'  => 'required',
        ]);
        //
        $tasks = Tasks::find($id);
        $tasks->name            = $request->get('name');
        $tasks->description     = $request->get('description') !=  "" ? $request->get('description') : "sin descripción.";
        $tasks->update();
        return;
    }

    public function destroy($id){
        $tasks = Tasks::find($id);
        $tasks->deleted = time();
        if($tasks->Tasks_contents){
            foreach ($tasks->Tasks_contents as $tasks_contents) {
                $tasks_contents->deleted = time();
                $tasks_contents->update();
                if($tasks_contents->Tasks->type == 'groups'){
                    foreach ($tasks_contents->Tcxts as $tcxts) {
                        $tcxts->deleted = time();
                        $tcxts->update();
                    }
                }
            }
        }
        $tasks->update();
        return;
    }
}
