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
use App\Categories;
use DB;

class ReportsController extends Controller
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
        return view('reports.index');
    }

    public function tasks(){
        return view('reports.tasks');
    }

    public function task(){
        return view('reports.task');
    }

    public function statitics(){
        return view('reports.statitics');
    }

    public function hystoric(){
        return view('reports.hystoric');
    }

    public function list(){
        $admins_id = '';
        //Obtener todas los Años lectivos
        if(\Auth::user()->type == 'admins'){
            $admins_id = \Auth::user()->Admins->id;
        } else if(\Auth::user()->type == 'coordinators') {
            $admins_id = \Auth::user()->Coordinators->admins_id;
        } else if(\Auth::user()->type == 'teachers') {   
            $admins_id = \Auth::user()->Teachers->admins_id;
        }
        $school_years_ids = [];
        $teachers = [];
        $teaching_periods = [];
        $teachers_data = Teachers::with('Users')->select('id','users_id')->where('admins_id', $admins_id)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        foreach ($teachers_data as $value) {
            $teachers[] = ["name" => $value->users->name.' '.$value->users->lastname.' ('.$value->users->doc.')', "id" => $value->id];
        } 
        unset($teachers_data);
        $school_years = School_years::where('admins_id', $admins_id)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        foreach ($school_years as $value) {
            $school_years_ids[] = $value->id;
        }
        $teaching_periods_data = Teaching_periods::with('School_years')->whereIn('school_years_id', $school_years_ids)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        foreach ($teaching_periods_data as $value) {
            $teaching_periods[] = ["name" => $value->name,"namexsy" => $value->name.' - '.$value->school_years->name, "school_years" => $value->school_years, "id" => $value->id];
        }
        unset($teaching_periods_data);
        $data = [
            'auth'  => \Auth::user()->type,
            'teachers' => $teachers,
            'school_years' => $school_years,
            'teaching_periods' => $teaching_periods,
        ];
        unset($teachers);
        unset($school_years);
        unset($teaching_periods);
        return response()->json($data, 200);
    }

    public function list_hystoric(){
        if(\Auth::user()->type == 'admins'){
            $config = Configs::select(['id','school_years_id','teaching_periods_id','admins_id'])->where('admins_id', \Auth::user()->Admins->id)->where('deleted', 0)->first();
            $configs = $config;
            unset($config);
        } else if(\Auth::user()->type == 'coordinators') {
            $config = Configs::select(['id','school_years_id','teaching_periods_id','admins_id'])->where('admins_id', \Auth::user()->Coordinators->admins_id)->where('deleted', 0)->first();
            $configs = $config;
            unset($config);
        } 
        $school_years = School_years::where('admins_id', $configs->admins_id)->select('id', 'name')->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        $school_years_ids = [];
        foreach ($school_years as $value) {
            $school_years_ids[] = $value->id;
        }
        $teaching_periods = Teaching_periods::where('school_years_id', $configs->school_years_id)->select('id', 'name')->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        unset($school_years_ids);
        $data = [
            'tags'                  => Categories::select('id', 'name', 'description')->where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->get(),
            'school_years'          => $school_years,
            'school_years_id'       => $configs->school_years_id,
            'teaching_periods_id'   => $configs->teaching_periods_id,
            'teaching_periods'      => $teaching_periods,
            'activities'            => Tasks::where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->orderBy('id', 'desc')->get(),
        ];
        unset($school_years);
        unset($teaching_periods);
        return response()->json($data, 200);
    }

    public function reportByTeacher($teacher_id,$teaching_p_id){
        $query = Tasks_contents::select(
            array(
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE 
                    WHEN tasks_contents.status = 'finished' THEN 1 ELSE 0 END) AS finish"),
                DB::raw("SUM(CASE 
                    WHEN tasks_contents.status = 'unfulfilled' THEN 1 ELSE 0 END) AS unfulfilled"),
                DB::raw("SUM(CASE 
                    WHEN tasks_contents.status = 'unsatisfactory' THEN 1 ELSE 0 END) AS unsatisfactory"),
                DB::raw("SUM(CASE 
                    WHEN prologues.id IS NOT NULL THEN 1 ELSE 0 END) AS prologues"),
                DB::raw("SUM(CASE 
                    WHEN prologues.justifications IS NOT NULL THEN 1 ELSE 0 END) AS justifications"),
                DB::raw("SUM(DATEDIFF(tasks_contents.final_date, tasks_contents.start_date)) AS days"),
                DB::raw("SUM(CASE 
                    WHEN prologues.id IS NOT NULL THEN DATEDIFF(prologues.final_date, prologues.start_date) ELSE 0 END) AS extra")
            )
        )
        ->leftJoin('prologues', function($join) {
            $join->on('prologues.tasks_contents_id', '=', 'tasks_contents.id');
        })
        ->whereIn('tasks_contents.status',['finished', 'unfulfilled', 'unsatisfactory'])
        ->where('teaching_periods_id',$teaching_p_id);

        if ((int)$teacher_id > 0) {
            $tasks = $query->where('tasks_contents.teachers_id',$teacher_id)
                ->groupBy('teaching_periods_id', 'tasks_contents.teachers_id')->get();
        } else {
            $tasks = $query->groupBy('teaching_periods_id')->get();
        }

        


        return response()->json($tasks, 200);
    }

    public function reportByPeriod(Request $request){
        $tasks = Tasks_contents::select(
            array(
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE 
                    WHEN tasks_contents.status = 'finish' THEN 1 ELSE 0 END) AS finish"),
                DB::raw("SUM(CASE 
                    WHEN prologues.id IS NOT NULL THEN 1 ELSE 0 END) AS prologues"),
                DB::raw("SUM(CASE 
                    WHEN prologues.justifications IS NOT NULL THEN 1 ELSE 0 END) AS justifications"),
                DB::raw("SUM(DATEDIFF(tasks_contents.final_date, tasks_contents.start_date)) AS days"),
                DB::raw("SUM(CASE 
                    WHEN prologues.id IS NOT NULL THEN DATEDIFF(prologues.final_date, prologues.start_date) ELSE 0 END) AS extra")                
            )
        )
        ->leftJoin('prologues', function($join) {
            $join->on('prologues.tasks_contents_id', '=', 'tasks_contents.id');
        })
        ->where('teaching_periods_id',$request->get('teaching_p_id'))
        ->where('tasks_contents.status','finish')
        ->groupBy('teaching_periods_id', 'teachers_id')->get();

        return response()->json($tasks, 200);
    }

    public function tasksByPeriod($id){
        $configs = Configs::where('admins_id',\Auth::user()->coordinators->admins_id)->where('teaching_periods_id',$id)->where('deleted',0)->first();
        $method = 0;
        if($configs != false){
            $method = 1;
        }
        $tasks = Tasks_contents::with('Tasks','Teachers','Tcxts','Teaching_periods')->where('deleted',0)->where('teaching_periods_id',$id)->orderBy('id', 'desc')->paginate(15);
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
            'tasks' => $tasks->all(),
            'auth'  => $method,
            'tags'  => Categories::select('id', 'name', 'description')->where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->get(),
        ];
        return response()->json($data, 200);
    }

    public function task_list(){
        $school_years_ids = [];
        $school_years = School_years::where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        foreach ($school_years as $value) {
            $school_years_ids[] = $value->id;
        }
        $teaching_periods_data = Teaching_periods::with('School_years')->whereIn('school_years_id', $school_years_ids)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        foreach ($teaching_periods_data as $value) {
            $teaching_periods[] = ["name" => $value->name,"namexsy" => $value->name.' - '.$value->school_years->name, "school_years" => $value->school_years, "id" => $value->id];
        }
        unset($teaching_periods_data);
        $configs = Configs::where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted',0)->first();
        $data = [
            'school_years' => $school_years,
            'teaching_periods' => $teaching_periods,
            'teacher_id' => \Auth::user()->Teachers->id,
            'teaching_periods_id' => $configs->teaching_periods_id,
        ];
        unset($school_years);
        unset($teaching_periods);
        unset($configs);
        return response()->json($data, 200);
    }

}
