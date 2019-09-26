<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configs;
use App\School_years;
use App\Teaching_periods;

class ConfigsController extends Controller
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
        return view('configs.index');
    }

    public function list(){
        $configs = Configs::where('admins_id', \Auth::user()->Admins->id)->select('id','school_years_id','teaching_periods_id','admins_id')->where('deleted',0)->first();
        $data["configs"] = $configs;
        $school_years = School_years::where('admins_id', \Auth::user()->Admins->id)->where('deleted',0)->get();
        $data["school_years"] = $school_years;
        unset($school_years);

        if(isset($configs->school_years_id)){
            $teaching_periods = Teaching_periods::where('school_years_id', $configs->school_years_id)->where('deleted',0)->get();
            unset($configs);
            $data["teaching_periods"] = $teaching_periods;
            unset($teaching_periods);
        }
        return response()->json($data, 200);
    }

    public function getstatus(Request $request, $id){
        $configs = Configs::find($id);
        $configs->status = !$request->get('status');
        $configs->update();
        return;
    }

    public function store(Request $request){
        $this->validate($request, [
            'school_years_id'       => 'required',
            'teaching_periods_id'   => 'required',
            'admins_id'             => 'required'
        ]);
        //        //
        $configs = configs::where('admins_id', $request->get('admins_id'))->where('deleted',0)->first();
        $configs->school_years_id = $request->get('school_years_id');
        $configs->teaching_periods_id = $request->get('teaching_periods_id');
        $configs->update();
        return;
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'specialty'          => 'required',
            'profession'        => 'required',
        ]);
        //
        $configs = configs::find($id);
        $configs->specialty       = $request->get('specialty');
        $configs->profession      = $request->get('profession');
        $configs->status          = $request->get('status');
        $configs->update();
        //
        return;
    }

    public function destroy($id){
        $configs = configs::find($id);
        $configs->deleted = time();
        $configs->update();
        return;
    }

    public function school_years(){
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
}
