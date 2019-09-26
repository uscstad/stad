<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tasks;
use App\Tasks_contents;
use App\Configs;
use App\Prologues;
use App\School_years;
use App\Teaching_periods;
use App\Level_educations;
use App\Subjects;
use App\Coordinators;

class TaskController extends Controller
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
        \DB::update("UPDATE tasks_contents SET status='unfulfilled' WHERE status NOT IN ('finished','finishedtime') AND date_format(str_to_date(final_date,'%Y-%m-%dT%H:%i:%s.000Z'),'%Y-%m-%d %H:%i:%s') < CURRENT_TIMESTAMP(); ");
        //
        \DB::update("UPDATE tasks_contents SET status='unsatisfactory' WHERE status = 'pending' AND date_format(str_to_date(final_date,'%Y-%m-%dT%H:%i:%s.000Z'),'%Y-%m-%d %H:%i:%s') < CURRENT_TIMESTAMP(); ");
        //
        $tasks = Tasks_contents::with('Tasks','Teachers','Tcxts','Teaching_periods', 'Prologues')->where('teachers_id',\Auth::user()->Teachers->id)->where('deleted',0)->orderBy('id', 'asc')->get();
        $school_years_ids = [];
        $school_years = School_years::where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        foreach ($school_years as $value) {
            $school_years_ids[] = $value->id;
        }
        unset($school_years);
        $teaching_periods_data = Teaching_periods::with('School_years')->whereIn('school_years_id', $school_years_ids)->where('deleted',0)->orderBy('id', 'desc')->get()->all();
        unset($school_years_ids);
        foreach ($teaching_periods_data as $value) {
            $teaching_periods[] = ["name" => $value->name,"namexsy" => $value->name.' - '.$value->school_years->name, "school_years" => $value->school_years, "id" => $value->id];
        }
        $coordinators = Coordinators::with('Users')->where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get();
        $coordinators_data = [];
        foreach ($coordinators as $value) {
            $coordinators_data[] = ["name" => $value->Users->name.' '.$value->Users->lastname, "id" => $value->id];
        }
        unset($teaching_periods_data);
        $configs = Configs::where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted',0)->first();
        //
        $data = [
            'tasks'                 => $tasks,
            'teaching_periods'      => $teaching_periods,
            'teaching_periods_id'   => $configs->Teaching_periods->id,
            'teaching_periods_name' => $configs->Teaching_periods->name,
            'teaching_periods_start' => date("Y-m-d").' 00:00:00',
            'teaching_periods_final' => $configs->Teaching_periods->final_date,
            'level_educations'      => Level_educations::select('id','name','admins_id')->where('admins_id',\Auth::user()->Teachers->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get(),
            'subjects'              => Subjects::select('id','name','admins_id')->where('admins_id',\Auth::user()->Teachers->admins_id)->where('deleted',0)->orderBy('id', 'desc')->get(),
            'coordinators'          => $coordinators_data,
        ]; 
        unset($tasks);
        unset($configs);
        return view('task.index', compact('data'));
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
        tasks::create([
            'name'              => $request->get('name'),
            'description'       => $request->get('description') !=  "" ? $request->get('description') : "sin descripción.",
            'coordinators_id'   => \Auth::user()->Coordinators->id,
        ]);
        return;
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'prologue'              => 'required',
            'answer'                => 'required_with:annexed',
            'new_final_date'        => $request->get('evidence') ? 'required' : 'required_with:justifications',
            'justifications'        => $request->get('evidence') ? 'required' : 'required_with:new_final_date',
        ]);

        if ($request->answer) {
            $annexed = null;
            $this->validate($request, [
                'annexed'              => 'required_if:attachments,1'
            ]);
            if($request->get('attachments') == 1){
                $base = explode(',', $request->get('annexed'));
                $file = $base[1];
                $name = explode('/', $base[0]);
                $file = str_replace(' ', '+', $file);
                $fileName = \Auth::user()->id.'_'.time().'_'.str_replace(' ', '_', $name[0]);
                \File::put(public_path(). '/attachments/' . $fileName, base64_decode($file));
                $annexed = 'attachments/' . $fileName;
            }
            $tasks_content = Tasks_contents::find($id);
            $tasks_content->answer      = $request->answer;
            $tasks_content->attachments = $request->annexed ? 2 : 1;
            $tasks_content->archiveName = $annexed;
            $tasks_content->status      = 'processing';
            $tasks_content->save();
        }

        if ($request->new_final_date && $request->justifications && !$request->prologue) {
            $evidence = null;
            if($request->evidence != ''){
                $base = explode(',', $request->get('evidence'));
                $file = $base[1];
                $name = explode('/', $base[0]);
                $file = str_replace(' ', '+', $file);
                $fileName = \Auth::user()->id.'_'.time().'_'.$name[0];
                \File::put(public_path(). '/justifications/' . $fileName, base64_decode($file));
                $evidence = 'justifications/' . $fileName;
            }
            $tasks_content = Tasks_contents::find($id);
            $tasks_content->status = 'reprogrammed';
            $tasks_content->save();

            $user = Prologues::create([
                'start_date'  => $request->final_date,
                'final_date'       => $request->new_final_date,
                'justifications'  => $request->justifications,
                'archive'  => $evidence ? 1 : 0,
                'archiveName'  => $evidence,
                'tasks_contents_id'  => $id,
            ]);
        }

        return;
    }

    public function destroy($id){
        $tasks = tasks::find($id);
        $tasks->deleted = time();
        $tasks->Tasks_contents->deleted = time();
        $tasks->Tasks_contents->update();
        if($tasks->type == 'groups'){
            foreach ($tasks->Tasks_contents->Tcxts as $tcxts) {
                $tcxts->deleted = time();
                $tcxts->update();
            }
        }
        $tasks->update();
        return;
    }

    public function teachers(){
        $data = Teachers::with('Users')->where('admins_id', \Auth::user()->Teachers->admins_id)->where('deleted', 0)->get();
        foreach ($data as $value) {
            $teachers[] = ["name" => $value->users->name.' '.$value->users->lastname, "doc" => $value->users->doc, "id" => $value->id];
        }            
        //Retornar en json Años lectivos
        return response()->json($teachers, 200);
    }

    public function boards($id, $status){
        $tasks = Tasks_contents::find($id);
        $tasks->boards = $status;
        $tasks->update();
    }
}
