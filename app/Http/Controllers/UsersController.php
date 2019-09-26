<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UsersController extends Controller
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
        return view('users.index');
    }

    public function list(){
    	$users = User::with('Admins', 'Coordinators', 'Teachers', 'Auxiliaries')->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $users->previousPageUrl(),
                'next_page_url' => $users->nextPageUrl(),
                'total'         => $users->total(),
                'current_page'  => $users->currentPage(),
                'per_page'      => $users->perPage(),
                'last_page'     => $users->lastPage(),
                'from'          => $users->firstItem(),
                'to'            => $users->lastPage(),
            ],
            'users' => $users
        ]; 
        return response()->json($data, 200);
    }

    public function store(Request $request){
        // Se debe validar el email cuando este eliminado el usuario y que lo deje crear un usuario.
        $this->validate($request, [
            'name'      => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('deleted', 0);
                })
            ],
            'password'  => 'required',
            'status'    => 'required',
            'type'      => 'required',
        ]);

       	$user = User::create([
            'name'      => $request->get('name'),
            'email'     => $request->get('email'),
            'password'  => bcrypt($request->get('password')),
            'status'    => $request->get('status'),
            'type'      => $request->get('type'),
        ]);
        if($request->get('type') == 'student'){
            Students::create([
                'address'       => $request->students['address'],
                'district'      => $request->students['district'],
                'phone'         => $request->students['phone'],
                'indicative'    => $request->students['indicative'],
                'mobile'        => $request->students['mobile'],
                'attendant'     => $request->students['attendant'],
                'courses_id'    => $request->students['courses_id'],
                'schools_id'    => $request->students['schools_id'],
                'users_id'      => $user->id,
            ]);
        }
        return;
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name'     => 'required|max:191',
            'email'    => 'required|email|max:191',
            'status'   => 'required',
            'type'     => 'required'
        ]);



        $users = User::find($id);
        $users->name     = $request->get('name');
        $users->email    = $request->get('email');
        $users->status   = $request->get('status');
        $users->type     = $request->get('type');
        if($request->get('password') != "") {
            $users->password =  bcrypt($request->get('password'));
        }
        $users->update();
        if($request->get('type') == 'student'){
            $data = $request->get('students');
            //
            $students = Students::find($data['id']);
            $students->address      = $data['address'];  
            $students->district     = $data['district']; 
            $students->phone        = $data['phone']; 
            $students->indicative   = $data['indicative']; 
            $students->mobile       = $data['mobile']; 
            $students->attendant    = $data['attendant']; 
            $students->courses_id   = $data['courses_id']; 
            $students->schools_id   = $data['schools_id']; 
            $students->update();
        }
        return;
    }

    public function destroy($id){
        $users = User::find($id);
        $users->deleted = time();
        $users->update();
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
