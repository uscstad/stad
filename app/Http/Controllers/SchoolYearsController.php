<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\School_years;
use App\Teaching_periods;
use App\Admins;
use App\Configs;

class SchoolYearsController extends Controller
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
        return view('school_years.index');
    }

    public function list(){
        $school_years = School_years::where('admins_id', \Auth::user()->Admins->id)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $school_years->previousPageUrl(),
                'next_page_url' => $school_years->nextPageUrl(),
                'total'         => $school_years->total(),
                'current_page'  => $school_years->currentPage(),
                'per_page'      => $school_years->perPage(),
                'last_page'     => $school_years->lastPage(),
                'from'          => $school_years->firstItem(),
                'to'            => $school_years->lastPage(),
            ],
            'school_years' => $school_years
        ]; 
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name'          => ['required',
                Rule::unique('school_years')->where(function ($query) {
                    return $query->where('admins_id',\Auth::user()->Admins->id)->where('deleted', 0);
                })
            ],
            'status'        => 'required',
        ]);
        //
        $counnt = School_years::where('admins_id', \Auth::user()->Admins->id)->where('deleted',0)->count();
        if($counnt > 1){
            $school_years = School_years::create([
                'name'          => $request->get('name'),
                'description'   => $request->get('description') !=  "" ? $request->get('description') : "sin descripción.",
                'status'        => $request->get('status'),
                'admins_id'     => \Auth::user()->Admins->id,
            ]);
        } else {
           $school_years = School_years::create([
                'name'          => $request->get('name'),
                'description'   => $request->get('description') !=  "" ? $request->get('description') : "sin descripción.",
                'status'        => $request->get('status'),
                'admins_id'     => \Auth::user()->Admins->id,
            ]);
            //
            $configs = Configs::where('admins_id',\Auth::user()->Admins->id)->first();
            $configs->school_years_id = $school_years->id;
            $configs->update();
        }
        return;
    }

    public function update(Request $request, $id){
        $school_years = School_years::find($id);
        $this->validate($request, [
            'name'          => $request->get('name') == $school_years->name ? 'required' : ['required',
                Rule::unique('school_years')->where(function ($query) {
                    return $query->where('deleted', 0);
                })
            ],
            'status'        => 'required',
        ]);
        //
        $school_years->name            = $request->get('name');
        $school_years->description     = $request->get('description') !=  "" ? $request->get('description') : "sin descripción.";
        $school_years->status          = $request->get('status');
        $school_years->update();
        //
        return;
    }

    public function destroy($id){
        $school_years = School_years::find($id);
        $school_years->deleted = time();
        //$school_years->deleted = 0;
        $school_years->update();
        //
        $teaching_periods = Teaching_periods::where('school_years_id', $school_years->id)->where('deleted',0)->get();
        unset($school_years);
        //
        foreach ($teaching_periods as $teaching_period) {
            $teaching_period->status = 0;
            $teaching_period->deleted = time();
            $teaching_period->update();
        }
        unset($teaching_periods);
        return;
    }
}
