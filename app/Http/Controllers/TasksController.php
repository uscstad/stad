<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tasks;
use App\Tasks_contents;
use App\Coordinators;
use App\Tcxts;
use App\Teachers;
use App\Configs;
use App\Categories;
use App\Level_educations;
use App\Subjects;
use App\Prologues;
use App\School_years;
use App\Teaching_periods;
use Illuminate\Validation\Rule;

class TasksController extends Controller
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
        \DB::update("UPDATE tasks_contents SET status='processing' WHERE status = 'assigned' AND date_format(str_to_date(start_date,'%Y-%m-%dT%H:%i:%s.000Z'),'%Y-%m-%d %H:%i:%s') < CURRENT_TIMESTAMP(); ");
        //
        \DB::update("UPDATE tasks_contents SET status='unfulfilled' WHERE date_format(str_to_date(final_date,'%Y-%m-%dT%H:%i:%s.000Z'),'%Y-%m-%d %H:%i:%s') < CURRENT_TIMESTAMP(); ");
        //
        \DB::update("UPDATE tasks_contents SET status='unsatisfactory' WHERE status = 'pending' AND date_format(str_to_date(final_date,'%Y-%m-%dT%H:%i:%s.000Z'),'%Y-%m-%d %H:%i:%s') < CURRENT_TIMESTAMP(); ");
        //
        $tasks = Tasks_contents::with('Tasks','Teachers','Tcxts','Teaching_periods', 'Prologues')->whereHas('Tasks', function($q){
            $q->where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0);
        })->where('deleted',0)->orderBy('id', 'asc')->get();
        $configs = Configs::with('Teaching_periods')->where('admins_id',\Auth::user()->Coordinators->admins_id)->where('deleted',0)->first();
        $teacher = Teachers::with('Users', 'Suxts', 'Lexts')->where('admins_id', \Auth::user()->Coordinators->admins_id)->where('deleted', 0)->get();
        if(count($teacher) > 0){
            foreach ($teacher as $value) {
                $teachers[] = [
                    "name" => $value->users->name.' '.$value->users->lastname,
                    "doc" => $value->users->doc,
                    "suxts" => $value->suxts,
                    "lexts" => $value->lexts,
                    "id" => $value->id
                ];
            } 
        } else {
            $teachers = [];
        }
        unset($teacher); 
        $school_years = School_years::select('id')->where('admins_id',\Auth::user()->Coordinators->admins_id)->where('deleted',0)->get();
        $school_years_ids = [];
        foreach ($school_years as $value) {
            $school_years_ids[] = $value->id;
        }
        unset($school_years);
        $data = [
            'tasks'                 => $tasks->all(),
            'teaching_periods'      => Teaching_periods::select('id', 'name')->whereIn('school_years_id',$school_years_ids)->where('deleted',0)->get(),
            'teaching_periods_id'   => $configs->Teaching_periods->id,
            'teaching_periods_name' => $configs->Teaching_periods->name,
            'teaching_periods_start' => date("Y-m-d").' 00:00:00',
            'teaching_periods_final' => $configs->Teaching_periods->final_date,
            'categories'            => Categories::select('id', 'name', 'description')->where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->orderBy('id', 'desc')->get(),
            'activities'            => Tasks::select('id','name')->where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->orderBy('id', 'desc')->get(),
            'level_educations'      => Level_educations::select('id','name','admins_id')->where('admins_id',\Auth::user()->Coordinators->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get(),
            'subjects'              => Subjects::select('id','name','admins_id')->where('admins_id',\Auth::user()->Coordinators->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get(),
            'teachers'              => $teachers,
        ]; 
        unset($tasks);
        unset($configs);
        unset($teachers);
        $data = json_encode($data);
        return view('tasks.index', compact('data'));
    }

    public function getStatus(){
        //$tasks = Tasks_contents::whereRaw("DATE_FORMAT(start_date, '%Y-%m-%dT%H:%i:%s.000Z') = DATE_FORMAT(now(), '%Y-%m-%d %H:%i:%s') ")->where('status','assigned')->where('deleted',0)->first();
        $count = Tasks_contents::where('status','assigned')->count();
        if($count>0){
            $tasks = \DB::select("UPDATE tasks_contents SET `status` = 'processing' WHERE `tasks_contents`.`status` = 'assigned' AND date_format(str_to_date(`tasks_contents`.`start_date`,'%Y-%m-%dT%H:%i:%s.000Z'),'%Y-%m-%d %H:%i:%s') < CURRENT_TIMESTAMP () LIMIT 1;");
        }
        return true;
    }

    public function store(Request $request){
        $this->validate($request, [
            'tasks_id'              => 'required',
            'type'                  => 'required',
            'detail'                => 'required',              
            'attachments'           => 'required',
            'teacher'               => 'required',
            'start_date'            => 'required',
            'final_date'            => 'required',
            'teaching_periods_id'   => 'required',
            'teachers_fulls'        => 'required_if:type,==,groups',
        ]);
        // 
        /*if(count($request->teachers_fulls)>1){
            foreach ($request->teachers_fulls as $value) {
                
            } 
        }*/
        //

        foreach ($request->teacher as $value) {
            $categories = $request->categories != null ? json_encode($request->categories) : "[]";
            $tasks_content = Tasks_contents::create([
                'type'                  => $request->get('type'),
                'categories'            => $categories,
                'start_date'            => $request->get('start_date'),
                'final_date'            => $request->get('final_date'),
                'teachers_id'           => $value['id'],
                'teaching_periods_id'   => $request->get('teaching_periods_id'),
                'tasks_id'              => $request->get('tasks_id'),
                'attachments'           => $request->get('attachments'),
                'comments'              => $request->get('comments'),
                'detail'                => $request->get('detail'),                
                'level_educations'      => json_encode($request->get('selectLevel')),
                'subjects'              => json_encode($request->get('selectSubje')),
            ]);
            //
            $groups = (object) $request->get('teachers_fulls');
            if($request->type == "groups" && $groups != "No Data"){
                foreach ($groups as $key) {
                    Tcxts::create([
                        'tasks_contents_id' => $tasks_content->id,
                        'teachers_id'       => $key['id'],
                    ]);
                }
            }
            unset($tasks_content);
        }
        return;
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'tasks_id'              => 'required',
            'types'                 => 'required',  
            'detail'                => 'required',            
            'attachments'           => 'required',
            'teachers_id'           => 'required',
            'tags'                  => 'required',
            'start_date'            => 'required',
            'final_date'            => 'required',
            'teaching_periods_id'   => 'required',
            'teachers_group'        => 'required_if:type,==,groups',
        ]);
        //
        $tasks_content = Tasks_contents::find($id);
        $tasks_content->type                  = $request->types;        
        $tasks_content->categories            = json_encode($request->tags);
        $tasks_content->start_date            = $request->start_date;
        $tasks_content->final_date            = $request->final_date;
        $tasks_content->teachers_id           = $request->teachers_id;
        $tasks_content->teaching_periods_id   = $request->teaching_periods_id;
        $tasks_content->tasks_id              = $request->tasks_id;
        $tasks_content->comments              = $request->comments;
        $tasks_content->detail                = $request->detail;
        $tasks_content->level_educations      = $request->level_educations != "" ? json_encode($request->level_educations) : "";
        $tasks_content->subjects              = $request->subjects != "" ? json_encode($request->subjects) : "";
        $tasks_content->save();
 
        return;
    }

    public function destroy($id){
        $tasks = Tasks_contents::find($id);
        $tasks->deleted = time();
        if($tasks->Tasks->type == 'groups'){
            foreach ($tasks->Tcxts as $tcxts) {
                $tcxts->deleted = time();
                $tcxts->update();
            }
        }
        $tasks->update();
        return;
    }

    public function teachers(){
        $data = Teachers::with('Users')->where('admins_id', \Auth::user()->Coordinators->admins_id)->where('deleted', 0)->get();
        foreach ($data as $value) {
            $teachers[] = ["name" => $value->users->name.' '.$value->users->lastname, "doc" => $value->users->doc, "id" => $value->id];
        }            
        //Retornar en json Años lectivos
        return response()->json($teachers, 200);
    }
}