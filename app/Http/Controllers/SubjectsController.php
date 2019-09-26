<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subjects;
use Illuminate\Validation\Rule;

class SubjectsController extends Controller
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
        return view('subjects.index');
    }

    public function list(){

        $subjects = Subjects::where('admins_id',\Auth::user()->Admins->id)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $subjects->previousPageUrl(),
                'next_page_url' => $subjects->nextPageUrl(),
                'total'         => $subjects->total(),
                'current_page'  => $subjects->currentPage(),
                'per_page'      => $subjects->perPage(),
                'last_page'     => $subjects->lastPage(),
                'from'          => $subjects->firstItem(),
                'to'            => $subjects->lastPage(),
            ],
            'subjects' => $subjects->all(),
        ]; 
        unset($subjects);
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => ['required',
                Rule::unique('subjects')->where(function ($query) {
                    return $query->where('admins_id',\Auth::user()->Admins->id)->where('deleted', 0);
                })
            ],
        ]);
        //
        Subjects::create([
            'name'              => $request->get('name'),
            'description'       => $request->get('description') !=  "" ? $request->get('description') : "sin descripción.",
            'admins_id'   => \Auth::user()->Admins->id,
        ]);
        return;
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => ['required',
                Rule::unique('subjects')->where(function ($query) {
                    return $query->where('admins_id',\Auth::user()->Admins->id)->where('deleted', 0);
                })->ignore($request->get('id'))
            ],
        ]);
        //
        $subjects = Subjects::find($id);
        $subjects->name            = $request->get('name');
        $subjects->description     = $request->get('description') !=  "" ? $request->get('description') : "sin descripción.";
        $subjects->update();
        //
        return;
    }

    public function destroy($id){
        $subjects = Subjects::find($id);
        $subjects->deleted = time();
        if($subjects->Suxts){
        	foreach ($subjects->Suxts as $taxts) {
                $taxts->deleted = time();
                $taxts->update();
            }
        }
        $subjects->update();
        return;
    }
}
