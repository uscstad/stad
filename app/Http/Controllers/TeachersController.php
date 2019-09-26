<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Teachers;
use App\User;
use App\Admins;
use App\Level_educations;
use App\Lexts;
use App\Subjects;
use App\Suxts;

class TeachersController extends Controller
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
        return view('teachers.index');
    }

    public function list(){
        //Obtener todas los Años lectivos
        if(\Auth::user()->type == 'admins'){
            $admins_id = \Auth::user()->Admins->id;
        } else if(\Auth::user()->type == 'coordinators') {
            $admins_id = \Auth::user()->Coordinators->admins_id;
        }
        $teachers = Teachers::with('Users','Lexts','Suxts')->where('admins_id', $admins_id)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $teachers->previousPageUrl(),
                'next_page_url' => $teachers->nextPageUrl(),
                'total'         => $teachers->total(),
                'current_page'  => $teachers->currentPage(),
                'per_page'      => $teachers->perPage(),
                'last_page'     => $teachers->lastPage(),
                'from'          => $teachers->firstItem(),
                'to'            => $teachers->lastPage(),
            ],
            'teachers'          => $teachers->all(),
            'level_educations'   => Level_educations::select('id','name')->where('admins_id',$admins_id)->where('deleted',0)->orderBy('id', 'desc')->get(),
            'subjects'          => Subjects::select('id','name')->where('admins_id',$admins_id)->where('deleted',0)->orderBy('id', 'desc')->get(),
        ]; 
        unset($teachers);
        return response()->json($data, 200);
    }

    public function store(Request $request){
        // Se debe validar el email cuando este eliminado el usuario y que lo deje crear un usuario.
        $this->validate($request, [
        	'type_doc'              => 'required',
        	'doc'                   => 'required',
        	'name'                  => 'required|max:191',
            'lastname'              => 'required',
            'user'                  => 'required',
            'email'                 => ['required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('deleted', 0);
                })
            ],
            'password'           => 'required',
            'address'            => 'required',
            'mobile'             => 'required',
            'phone'              => 'required',
            'level_educations'   => 'required',
            'subjects'           => 'required',
        ]);
        //
        if(\Auth::user()->type == 'admins'){
            $admins_id = \Auth::user()->Admins->id;
        } else if(\Auth::user()->type == 'coordinators') {
            $admins_id = \Auth::user()->Coordinators->admins_id;
        }
        //
       	$user = User::create([
            'type_doc'  => $request->get('type_doc'),
            'doc'       => $request->get('doc'),
            'lastname'  => $request->get('lastname'),
            'name'      => $request->get('name'),
            'user'      => $request->get('user'),
            'email'     => $request->get('email'),
            'password'  => bcrypt($request->get('password')),
            'address'   => $request->get('address'),
            'mobile'    => $request->get('mobile'),
            'phone'     => $request->get('phone'),
            'type'      => 'teachers',
        ]);
        $teacher = Teachers::create([
            'specialty'     => $request->get('specialty'),
            'profession'    => $request->get('profession'),
            'training'      => $request->get('training'),
            'scale'         => $request->get('scale'),
            'users_id'      => $user->id,
            'admins_id'     => $admins_id,
        ]);
        //
        foreach ($request->level_educations as $value) {
            Lexts::create([
                'level_educations_id'   => $value['id'],
                'teachers_id'           => $teacher->id,
            ]);
        }
        //
        foreach ($request->subjects as $value) {
            Suxts::create([
                'subjects_id'   => $value['id'],
                'teachers_id'   => $teacher->id,
            ]);
        }
        return;
    }

    public function update(Request $request, $id){
        $teacher = Teachers::find($id);
        $uxteacher = User::find($teacher->users_id);
        $this->validate($request, [
            'type_doc'           => 'required',
            'doc'                => 'required',
            'name'               => 'required|max:191',
            'lastname'           => 'required|max:191',
            'user'               => 'required',
            'email'              => $request->get('email') == $uxteacher->email ? 'required' : ['required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('deleted', 0);
                })
            ],
            'address'            => 'required',
            'mobile'             => 'required',
            'phone'              => 'required',
            'level_educations'   => 'required',
            'subjects'           => 'required',
        ]);


        $teacher->specialty     = $request->get('specialty');
        $teacher->profession    = $request->get('profession');
        $teacher->training      = $request->get('training');
        $teacher->scale         = $request->get('scale');

        
        $uxteacher->type_doc   = $request->get('type_doc');
        $uxteacher->doc        = $request->get('doc');
        $uxteacher->lastname   = $request->get('lastname');
        $uxteacher->name       = $request->get('name');
        $uxteacher->user       = $request->get('user');
        $uxteacher->email      = $request->get('email');
        if($request->get('password') != "") {
            $uxteacher->password =  bcrypt($request->get('password'));
        }
        $uxteacher->address    = $request->get('address');
        $uxteacher->mobile     = $request->get('mobile');
        $uxteacher->phone      = $request->get('phone');

        $teacher->update();
        $uxteacher->update();
        //
        $id_level_educations_new = array();
        foreach ($request->level_educations as $value) {
            $id_level_educations_new[] = (int) $value['id'];
        } 
        //   
        $lexts = Lexts::select('level_educations_id', 'teachers_id')->where('teachers_id',$teacher->id)->where('deleted',0)->get();
        $id_level_educations_old = array();
        foreach ($lexts as $value) {
            $id_level_educations_old[] = (int) $value->level_educations_id;
        }
        unset($lexts);
        //
        $delete = array_values(array_diff($id_level_educations_old, $id_level_educations_new));
        if($delete != false){
            $lexts = Lexts::select('level_educations_id', 'teachers_id','deleted')->whereIn('level_educations_id',$delete)->where('teachers_id',$teacher->id)->where('deleted',0)->update(['deleted' => time()]);
            unset($delete);
        }
        //
        $create = array_values(array_diff($id_level_educations_new, $id_level_educations_old));
        unset($id_level_educations_new);
        unset($id_level_educations_old);
        if($create != false){
            foreach ($create as $level_educations_id) {
                Lexts::create([
                    'level_educations_id'   => $level_educations_id,
                    'teachers_id'           => $teacher->id,
                ]);
            }
            unset($create);
        }
        //
        $id_subjects_new = array();
        foreach ($request->subjects as $value) {
            $id_subjects_new[] = (int) $value['id'];
        } 
        //   
        $suxts = Suxts::select('subjects_id', 'teachers_id')->where('teachers_id',$teacher->id)->where('deleted',0)->get();
        $id_subjects_old = array();
        foreach ($suxts as $value) {
            $id_subjects_old[] = (int) $value->subjects_id;
        }
        unset($suxts);
        //
        $delete = array_values(array_diff($id_subjects_old, $id_subjects_new));
        if($delete != false){
            $suxts = Suxts::select('subjects_id', 'teachers_id','deleted')->whereIn('subjects_id',$delete)->where('teachers_id',$teacher->id)->where('deleted',0)->update(['deleted' => time()]);
            unset($delete);
        }
        //
        $create = array_values(array_diff($id_subjects_new, $id_subjects_old));
        unset($id_subjects_new);
        unset($id_subjects_old);
        if($create != false){
            foreach ($create as $subjects_id) {
                Suxts::create([
                    'subjects_id'   => $subjects_id,
                    'teachers_id'   => $teacher->id,
                ]);
            }
            unset($create);
        }
        return;
    }

    public function destroy($id){
        $teachers = Teachers::find($id);
        $teachers->deleted = time();
        $teachers->Users->deleted = time();
        $teachers->Users->update();
        //
        foreach ($teachers->Lexts as $value) {
            $value->deleted = time();
            $value->update();
        }
        //
        foreach ($teachers->Suxts as $value) {
            $value->deleted = time();
            $value->update();
        }
        $teachers->update();
        return;
    }

    public function postPassword(Request $request){
        $this->validate($request, [
            'current'               => 'required',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);
        $user = User::find(Auth::id());

        if (!Hash::check($request->get('current'), $user->password)) {
            return response()->json(['errors' => ['password'=> ['Current password does not match']]], 422);
        }

        $user->password = Hash::make($request->get('password'));
        $user->save();

        return $user;
    }
}
