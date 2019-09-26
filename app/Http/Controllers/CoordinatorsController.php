<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coordinators;
use Illuminate\Validation\Rule;
use App\Admins;
use App\User;

class CoordinatorsController extends Controller
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
        return view('coordinators.index');
    }

    public function list(){
        $coordinators = Coordinators::with('Users')->where('admins_id', \Auth::user()->Admins->id)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $coordinators->previousPageUrl(),
                'next_page_url' => $coordinators->nextPageUrl(),
                'total'         => $coordinators->total(),
                'current_page'  => $coordinators->currentPage(),
                'per_page'      => $coordinators->perPage(),
                'last_page'     => $coordinators->lastPage(),
                'from'          => $coordinators->firstItem(),
                'to'            => $coordinators->lastPage(),
            ],
            'coordinators' => $coordinators
        ]; 
        return response()->json($data, 200);
    }

    public function getstatus(Request $request, $id){
        $coordinators = Coordinators::find($id);
        $coordinators->status = !$request->get('status');
        $coordinators->update();
        return;
    }

    public function store(Request $request){
        $this->validate($request, [
            'type_doc'      => 'required',
            'doc'           => 'required',
            'name'          => 'required|max:191',
            'lastname'      => 'required|max:191',
            'user'          => 'required',
            'email'         => ['required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('deleted', 0);
                })
            ],
            'password'      => 'required',
            'address'       => 'required',
            'mobile'        => 'required',
            'phone'         => 'required',
            'specialty'     => 'required',
            'profession'    => 'required',
            'status'        => 'required',
        ]);
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
            'status'    => $request->get('status'),
            'type'      => 'coordinators',
        ]);
        //
        Coordinators::create([
            'specialty'     => $request->get('specialty'),
            'profession'    => $request->get('profession'),
            'status'        => $request->get('status'),
            'admins_id'	    => \Auth::user()->Admins->id,
            'users_id'      => $user->id,
        ]);
        return;
    }

    public function update(Request $request, $id){
        $coordinators = Coordinators::find($id);
        $uxcoordinators = User::find($coordinators->users_id);
        $this->validate($request, [
            'type_doc'      => 'required',
            'doc'           => 'required',
            'name'          => 'required|max:191',
            'lastname'      => 'required|max:191',
            'user'          => 'required',
            'email'         => $request->get('email') == $uxcoordinators->email ? 'required' : ['required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('deleted', 0);
                })
            ],
            'address'     => 'required',
            'mobile'      => 'required',
            'phone'       => 'required',
            'specialty'   => 'required',
            'profession'  => 'required',
            'type'        => 'required',
            'status'      => 'required',
        ]);


        $uxcoordinators->type_doc   = $request->get('type_doc');
        $uxcoordinators->doc        = $request->get('doc');
        $uxcoordinators->lastname   = $request->get('lastname');
        $uxcoordinators->name       = $request->get('name');
        $uxcoordinators->user       = $request->get('user');
        $uxcoordinators->email      = $request->get('email');
        if($request->get('password') != "") {
            $uxcoordinators->password =  bcrypt($request->get('password'));
        }
        $uxcoordinators->address    = $request->get('address');
        $uxcoordinators->mobile     = $request->get('mobile');
        $uxcoordinators->phone      = $request->get('phone');
        $uxcoordinators->status     = $request->get('status');
        $uxcoordinators->type       = $request->get('type');


        $coordinators->specialty       = $request->get('specialty');
        $coordinators->profession      = $request->get('profession');
        $coordinators->status          = $request->get('status');
        $uxcoordinators->update();
        $coordinators->update();
        //
        return;
    }

    public function destroy($id){
        $coordinators = Coordinators::find($id);
        $coordinators->deleted = time();
        $coordinators->update();
        return;
    }
}
