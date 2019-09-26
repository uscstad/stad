<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use DB;
use App\Teaching_periods;
use App\School_years;
use App\Admins;
use App\Configs;

class TeachingPeriodsController extends Controller
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
        return view('teaching_periods.index');
    }

    public function list(){   

        $teaching_periods = DB::table('teaching_periods')
            ->join('school_years', 'school_years.id', '=', 'teaching_periods.school_years_id')
            ->join('admins', 'admins.id', '=', 'school_years.admins_id')
            ->select('teaching_periods.*','school_years.id as ids_y','school_years.name as names_y')
            ->where('admins.id', \Auth::user()->Admins->id)
            ->where('teaching_periods.deleted',0)
            ->orderBy('teaching_periods.id', 'desc')
            ->paginate(15);
        //$teaching_periods = Teaching_periods::with('School_years')->where('school_years.admins_id', 1)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $teaching_periods->previousPageUrl(),
                'next_page_url' => $teaching_periods->nextPageUrl(),
                'total'         => $teaching_periods->total(),
                'current_page'  => $teaching_periods->currentPage(),
                'per_page'      => $teaching_periods->perPage(),
                'last_page'     => $teaching_periods->lastPage(),
                'from'          => $teaching_periods->firstItem(),
                'to'            => $teaching_periods->lastPage(),
            ],
            'teaching_periods' => $teaching_periods,
            'current_date' => date("Y-m-d")
        ]; 
        return response()->json($data, 200);
    }

    public function getstatus(Request $request, $id){
        $teaching_periods = School_years::find($id);
        $teaching_periods->status = !$request->get('status');
        $teaching_periods->update();
        return;
    }

    public function store(Request $request){
        $this->validate($request, [
            'name'          	=> ['required',
                Rule::unique('teaching_periods')->where(function ($query) use($request) {
                    return $query->where('deleted', 0)->where('school_years_id', $request->get('school_years_id'));
                })
            ],
            'start_date'        => 'required',
            'final_date'        => 'required|date|after:start_date',
            'status'        	=> 'required',
            'school_years_id'	=> 'required',
        ]);
        //
        $exp = explode(" ",$request->get('start_date'));
        $dates = Teaching_periods::whereRaw("final_date >= '".$exp[0]." 23:59:00'")->where('deleted',0)->where('school_years_id',$request->get('school_years_id'))->first();
        if ($dates) {
            return (new Response([
                'errors'=>[
                        'start_date' => ["The selected start date is invalid."]
                ]
            ], 500));
        }
        //
        $configs = Configs::where('admins_id',\Auth::user()->Admins->id)->where('school_years_id',$request->get('school_years_id'))->where('deleted',0)->first();
        //
        $teaching_periods = Teaching_periods::create([
            'name'              => $request->get('name'),
            'description'       => $request->get('description') !=  "" ? $request->get('description') : "sin descripción.",
            'start_date'        => $request->get('start_date'),
            'final_date'        => $request->get('final_date'),
            'status'            => $request->get('status'),
            'school_years_id'   => $request->get('school_years_id'),
        ]);
        if($configs != false && !isset($configs->teaching_periods_id)){
            $configs->teaching_periods_id = $teaching_periods->id;
            $configs->update();
        } 
        unset($configs);
        unset($teaching_periods);
        return;
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name'              => 'required',
            'start_date'        => 'required',
            'final_date'        => 'required',
            'status'            => 'required',
            'school_years_id'   => 'required',
        ]);
        //
        $teaching_periods = Teaching_periods::find($id);
        $teaching_periods->name            = $request->get('name');
        $teaching_periods->description     = $request->get('description') !=  "" ? $request->get('description') : "sin descripción.";
        $teaching_periods->start_date      = $request->get('start_date');
        $teaching_periods->final_date      = $request->get('final_date');
        $teaching_periods->status          = $request->get('status');
        $teaching_periods->school_years_id = $request->get('school_years_id');
        $teaching_periods->update();
        //
        return $teaching_periods;
    }

    public function destroy($id){
        $teaching_periods = Teaching_periods::find($id);
        $teaching_periods->deleted = time();
        $teaching_periods->update();
        return;
    }

    public function getAllSchool_years(){
        //Obtener todas los Años lectivos
        $school_years = School_years::select('name','id')->where('admins_id', \Auth::user()->Admins->id)->where('deleted',0)->orderBy('id','DESC')->get();
        //Retornar en json los Años lectivos
        return response()->json($school_years, 200);
    }
}
