<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Messages_contents;
use App\Tasks;
use App\Tasks_contents;
use App\User;
use App\Tcxts;
use App\Coordinators;
use App\Teachers;

class MessagesContentsController extends Controller
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
        return view('messages.index');
    }

    public function list(){
        $messages_received = Messages_contents::with('Tasks_contents','Users')->select('id', 'body', 'senders', 'tasks_contents_id', 'users_id', 'created_at','deleted AS messages_received')->where('users_id', \Auth::user()->id)->where('deleted',0)->orderBy('id', 'desc');
        //
        $messages = Messages_contents::with('Tasks_contents','Users')->select('id', 'body', 'senders', 'tasks_contents_id', 'users_id', 'created_at','deleted AS messages_sent')->where('senders','like','%"id":'.\Auth::user()->id.'%')->where('deleted',0)->union($messages_received)->orderBy('id', 'desc')->paginate(15);
        //
        $data = [
            'pagination' => [
                'prev_page_url' => $messages->previousPageUrl(),
                'next_page_url' => $messages->nextPageUrl(),
                'total'         => $messages->total(),
                'current_page'  => $messages->currentPage(),
                'per_page'      => $messages->perPage(),
                'last_page'     => $messages->lastPage(),
                'from'          => $messages->firstItem(),
                'to'            => $messages->lastPage(),
            ],
            'messages'          => $messages->all(),
            'users_id'          => \Auth::user()->id, 
        ]; 
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'body'      => 'required',
        ]);
        //
        Messages_contents::create([
            'body' 				=> $request->get('body'),
            'senders'           => $request->get('senders') != "" ? json_encode($request->get('senders')) : "",
            'tasks_contents_id' => $request->get('tasks_contents_id'),
            'users_id'			=> \Auth::user()->id,
            'status'            => 0,
        ]);
        //
        return;
    }

    public function getTasks(){
        $tasks = array();
        //
        if(\Auth::user()->type == 'coordinators') {
            $coordinator = \Auth::user()->Coordinators;
            foreach ($coordinator->Tasks as $value) {
                foreach ($value->Tasks_contents as $key) {
                    $tasks[] = ["id" => $key->id, "name" => $key->Tasks->name.' ('.$key->Tasks->id.')'];
                }
            }
            unset($coordinator);
        } else if(\Auth::user()->type == 'teachers') {   
            $teacher = \Auth::user()->Teachers;
            $tasks_contents = Tasks_contents::where('teachers_id', $teacher->id)->where('deleted',0)->get();
            foreach ($tasks_contents as $value) {
                $tasks[] = ["id" => $value->id, "name" => $value->Tasks->name.' ('.$value->Tasks->id.')'];
            }
            unset($tasks_contents);
            //
            $tcxts = Tcxts::where('teachers_id', $teacher->id)->where('deleted',0)->get();
            unset($teacher);
            foreach ($tcxts as $value) {
                if($value->Tasks_contents != ""){
                    $tasks[] = ["id" => $value->Tasks_contents->tasks_id, "name" => $value->Tasks_contents->Tasks->name.' ('.$value->Tasks_contents->Tasks->id.')'];
                }
            }
            unset($tcxts);
        }
        //Obtener todas las Tareas
        //$tasks = Tasks_contents::with('Tasks')->select('tasks_id','id')->whereIn('id', $tasks)->where('deleted',0)->orderBy('id','DESC')->get();
        //Retornar en json las Tareas
        return response()->json($tasks, 200);
    }

    public function getAllSenders(Request $request){
        $users = array();
        //
        if(\Auth::user()->type == 'admins'){
            $admins_id = \Auth::user()->Admins->id;
            $coordinators = Coordinators::where('admins_id', $admins_id)->where('deleted',0)->get();
            foreach ($coordinators as $value) {
                $users[] = $value->users_id;
            }
            unset($coordinators);
            //
            $teachers = Teachers::where('admins_id', $admins_id)->where('deleted',0)->get();
            unset($admins);
            foreach ($teachers as $value) {
                $users[] = $value->users_id;
            }
            unset($teachers);
        }
        if(\Auth::user()->type == 'coordinators') {
            $admins_id = \Auth::user()->Coordinators->admins_id;
            $coordinators = Coordinators::where('admins_id', $admins_id)->where('deleted',0)->get();
            foreach ($coordinators as $value) {
                $users[] = $value->users_id;
            }
            unset($coordinators);
            //
            $teachers = Teachers::where('admins_id', $admins_id)->where('deleted',0)->get();
            unset($coordinator);
            foreach ($teachers as $value) {
                $users[] = $value->users_id;
            }
            unset($teachers);
        } 
        if(\Auth::user()->type == 'teachers') {   
            $admins_id = \Auth::user()->Teachers->admins_id;
            $coordinators = Coordinators::where('admins_id', $admins_id)->where('deleted',0)->get();
            foreach ($coordinators as $value) {
                $users[] = $value->users_id;
            }
            unset($coordinators);
            //
            $teachers = Teachers::where('admins_id', $admins_id)->where('deleted',0)->get();
            unset($teacher);
            foreach ($teachers as $value) {
                $users[] = $value->users_id;
            }
            unset($teachers);
        }
        //
        $users_id[] = \Auth::user()->id;
        $users = array_values(array_diff($users, $users_id));
        unset($users_id);
        //Obtener todas los Usuarios
        $users = User::select(\DB::raw("id, CONCAT(name, ' ', lastname) as name"))->whereIn('id', $users)->where('deleted',0)->orderBy('id','DESC')->get();
        //Retornar en json las Usuarios
        return response()->json($users, 200);
    }

    public function getSenders($id){
        $users = array();
        //
        $tasks = Tasks_contents::find($id);
        $coordinators = $tasks->Tasks->Coordinators;
        $users[] = $coordinators->users_id;
        unset($coordinators);
        //
        $users[] = $tasks->Teachers->users_id;
        if($tasks->type == 'groups'){
            $tcxts = Tcxts::where('tasks_contents_id', $tasks_contents->id)->where('deleted',0)->get();
            unset($tasks_contents);
            foreach ($tcxts as $value) {
                $users[] = $value->Teachers->users_id;
            }
            unset($tcxts);
        }
        unset($tasks);
        unset($tasks_contents);
        //
        $users_id[] = \Auth::user()->id;
        $users = array_values(array_diff($users, $users_id));
        unset($users_id);
        //Obtener todas los Usuarios
        $users = User::select(\DB::raw("id, CONCAT(name, ' ', lastname) as name"))->whereIn('id', $users)->where('deleted',0)->orderBy('id','DESC')->get();
        //Retornar en json las Usuarios
        return response()->json($users, 200);
    }
}
