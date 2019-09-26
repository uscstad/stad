<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Level_educations;
use Illuminate\Validation\Rule;

class LevelEducationsController extends Controller
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
        return view('level_educations.index');
    }

    public function list(){

        $level_educations = Level_educations::where('admins_id',\Auth::user()->Admins->id)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $level_educations->previousPageUrl(),
                'next_page_url' => $level_educations->nextPageUrl(),
                'total'         => $level_educations->total(),
                'current_page'  => $level_educations->currentPage(),
                'per_page'      => $level_educations->perPage(),
                'last_page'     => $level_educations->lastPage(),
                'from'          => $level_educations->firstItem(),
                'to'            => $level_educations->lastPage(),
            ],
            'level_educations' => $level_educations->all(),
        ]; 
        unset($level_educations);
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => ['required',
                Rule::unique('level_educations')->where(function ($query) {
                    return $query->where('admins_id',\Auth::user()->Admins->id)->where('deleted', 0);
                })
            ],
        ]);
        //
        Level_educations::create([
            'name'              => $request->get('name'),
            'description'       => $request->get('description') !=  "" ? $request->get('description') : "sin descripción.",
            'admins_id'   => \Auth::user()->Admins->id,
        ]);
        return;
    }

    public function update(Request $request, $id){
        //
        $this->validate($request, [
            'name' => ['required',
                Rule::unique('level_educations')->where(function ($query) {
                    return $query->where('admins_id',\Auth::user()->Admins->id)->where('deleted', 0);
                })->ignore($request->get('id'))
            ],
        ]);
        //
        $level_educations = Level_educations::find($id);
        $level_educations->name            = $request->get('name');
        $level_educations->description     = $request->get('description') !=  "" ? $request->get('description') : "sin descripción.";
        $level_educations->update();
        //
        return;
    }

    public function destroy($id){
        $level_educations = level_educations::find($id);
        $level_educations->deleted = time();
        if($level_educations->Lexts){
        	foreach ($level_educations->Lexts as $taxts) {
                $taxts->deleted = time();
                $taxts->update();
            }
        }
        $level_educations->update();
        return;
    }
}
