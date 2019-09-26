<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\School_years;
use App\Teaching_periods;
use App\Tasks;
use App\Coordinators;
use App\Teachers;
use App\Admins;
use App\Tasks_contents;
use App\Messages_contents;
use App\Configs;

class DashboardsController extends Controller
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
        return view('dashboards.index');
    }


    public function report(){
        return view('dashboards.report');
    }

    public function manage(){
        return view('dashboards.manage');
    }

    public function list(Request $request){
        //Obtener todas los Años lectivos
        if(\Auth::user()->type == 'admins'){
            $config = Configs::select(['id','school_years_id','teaching_periods_id'])->where('admins_id', \Auth::user()->Admins->id)->where('deleted', 0)->first();
            $configs = $config;
            unset($config);
            $tasks_contents = $configs->teaching_periods->tasks_contents;
        } else if(\Auth::user()->type == 'coordinators') {
            $config = Configs::select(['id','school_years_id','teaching_periods_id'])->where('admins_id', \Auth::user()->Coordinators->admins_id)->where('deleted', 0)->first();
            $configs = $config;
            unset($config);
            $tasks_contents = $configs->teaching_periods->tasks_contents;
        } else if(\Auth::user()->type == 'teachers') {   
            $config = Configs::select(['id','school_years_id','teaching_periods_id'])->where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted', 0)->first();
            $configs = $config;
            unset($config);
            $tasks_contents = $configs->teaching_periods->tasks_contents;
        }
        $data = [
            'sesion' => \Auth::user()->type,
            'users_id' => \Auth::user()->id,
            'school_years_id' => $configs->school_years_id,
            'teaching_periods_id' => $configs->teaching_periods_id,
            'tasks_contents' => $tasks_contents
        ];
        unset($configs);
        unset($tasks_contents);
        return response()->json($data, 200);
    }


    public function list_search(Request $request){
        //Obtener todas los Años lectivos
        if(\Auth::user()->type == 'admins'){
            $teachers_data = Teachers::with('Users')->where('admins_id', \Auth::user()->Admins->id)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        } else if(\Auth::user()->type == 'coordinators') {
            $teachers_data = Teachers::with('Users')->where('admins_id', \Auth::user()->coordinators->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        } else if(\Auth::user()->type == 'teachers') {   
            $teachers_data = Teachers::with('Users')->where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        }
        $teachers = [];
        foreach ($teachers_data as $value) {
            $teachers[] = ["name" => $value->users->name.' '.$value->users->lastname.' ('.$value->users->doc.')', "id" => $value->id];
        } 
        unset($teachers_data);
        $data = [
            'teachers' => $teachers
        ];
        unset($teachers);
        return response()->json($data, 200);
    }

    public function school_years(Request $request){
        //Obtener todas los Años lectivos
        if(\Auth::user()->type == 'coordinators'){
            $school_years = School_years::select('name', 'id')->where('admins_id', \Auth::user()->Coordinators->admins_id)->where('deleted', 0)->get();
        } else if(\Auth::user()->type == 'admins') {
            $school_years = School_years::select('name','id')->where('admins_id', \Auth::user()->Admins->id)->where('deleted', 0)->get();
        } else if(\Auth::user()->type == 'teachers') {   
        	$school_years = School_years::select('name','id')->where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted', 0)->get();
        }
        //Retornar en json Años lectivos
        return response()->json($school_years, 200);
    }

    public function teaching_periods($id){
    	//Obtener todas los Peridosos del Año lectivo Actual
        $teaching_periods = Teaching_periods::select('name', 'id')->where('school_years_id', $id)->where('deleted', 0)->get();
        //Retornar en json los Peridosos del Año lectivo Actual
        return response()->json($teaching_periods, 200);
    }

    public function teachers(Request $request){
        if(\Auth::user()->type == 'coordinators'){
            $data = Teachers::with('Users')->where('admins_id', \Auth::user()->Coordinators->admins_id)->where('deleted', 0)->get();
            foreach ($data as $value) {
                $teachers[] = ["name" => $value->users->name.' '.$value->users->lastname, "doc" => $value->users->doc, "id" => $value->id];
            }            
        } else {
            $teachers = [];
        }
        //Retornar en json Años lectivos
        return response()->json($teachers, 200);
    }

    public function messages($id){
        $tasks_contents = Tasks_contents::find($id);
        //
        $messages = Messages_contents::with('Users')->where('tasks_contents_id', $tasks_contents->id)->where('deleted', 0)->get();
        //Retornar en json Años lectivos
        $data = [
            "data" => $messages,
            "users_id" => \Auth::user()->id,
        ];
        return response()->json($data, 200);
    }


    public function tasksByTeacher($id){
        $tasks = Tasks_contents::join('tcxts', 'tasks_contents_id', '=', 'tasks_contents.id')->with('Tasks')->with('Teachers')->with('Tcxts')->with('Teaching_periods')->whereRaw('tcxts.deleted = 0 AND tasks_contents.deleted = 0 AND (tcxts.teachers_id = '.$id.' OR tasks_contents.teachers_id = '.$id.')')->select(['tasks_contents.id','tasks_contents.styles','tasks_contents.start_date','tasks_contents.final_date','tasks_contents.status','tasks_contents.deleted','tasks_contents.teachers_id','tasks_contents.teaching_periods_id','tasks_contents.tasks_id','tasks_contents.created_at','tasks_contents.updated_at'])->orderBy('id', 'desc')->paginate(15);
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
            'tasks' => $tasks
        ];
        return response()->json($data, 200);
    }


    public function tasksByName($name){
        $tasks = Tasks_contents::join('tasks', 'tasks.id', '=', 'tasks_id')->with('Tasks')->with('Teachers')->with('Tcxts')->with('Teaching_periods')->where('tasks_contents.deleted',0)->where('tasks.name', 'like', '%' . $name . '%')->select(['tasks_contents.id','tasks_contents.start_date','tasks_contents.final_date','tasks_contents.status','tasks_contents.deleted','tasks_contents.teachers_id','tasks_contents.teaching_periods_id','tasks_contents.tasks_id','tasks_contents.created_at','tasks_contents.updated_at'])->orderBy('tasks_contents.id', 'desc')->paginate(15);
        
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
            'tasks' => $tasks
        ];
        return response()->json($data, 200);
    }

    public function senders(Request $request, $id){
        //Obtener todas los Años lectivos
        if(\Auth::user()->type == 'coordinators'){
            $tasks = Tasks_contents::find($id);
            dd($tasks->Tasks);
            $configs = Configs::with('Teaching_periods')->where('admins_id', \Auth::user()->Admins->id)->where('deleted', 0)->first();
        } else if(\Auth::user()->type == 'teachers') {
            $configs = Configs::with('Teaching_periods')->where('admins_id', \Auth::user()->Coordinators->admins_id)->where('deleted', 0)->first();
        }
    }

    public function searchIndividualTasks($name, $status){
        $tasks = Tasks_contents::join('tasks', 'tasks.id', '=', 'tasks_id')->with('Tasks')->with('Teachers')->with('Teaching_periods')->where('tasks_contents.deleted',0)->select(['tasks_contents.id' ,'tasks_contents.start_date','tasks_contents.final_date','tasks_contents.status','tasks_contents.deleted','tasks_contents.teachers_id','tasks_contents.teaching_periods_id','tasks_contents.tasks_id','tasks_contents.created_at','tasks_contents.updated_at'])->where('tasks.type', 'individuals');
        if($name != 'none'){
            $tasks = $tasks->where('tasks.name', 'like', '%' . $name . '%');
        }
        if($status != 'none'){
            $tasks = $tasks->where('tasks_contents.status',  $status);   
        }
        $tasks = $tasks->orderBy('tasks_contents.id', 'desc')->paginate(15);
        
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
            'tasks' => $tasks
        ];
        return response()->json($data, 200);
    }

    public function searchColectiveTasks($name, $status){
        $tasks = Tasks_contents::join('tasks', 'tasks.id', '=', 'tasks_id')->with('Tasks')->with('Teachers')->with('Tcxts')->with('Teaching_periods')->where('tasks_contents.deleted',0)->select(['tasks_contents.id','tasks_contents.start_date','tasks_contents.final_date','tasks_contents.status','tasks_contents.deleted','tasks_contents.teachers_id','tasks_contents.teaching_periods_id','tasks_contents.tasks_id','tasks_contents.created_at','tasks_contents.updated_at'])->where('tasks.type', 'groups');
        if($name != 'none'){
            $tasks = $tasks->where('tasks.name', 'like', '%' . $name . '%');
        }
        if($status != 'none'){
            $tasks = $tasks->where('tasks_contents.status',  $status);   
        }
        $tasks = $tasks->orderBy('tasks_contents.id', 'desc')->paginate(15);
        
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
            'tasks' => $tasks
        ];
        return response()->json($data, 200);
    }
}
