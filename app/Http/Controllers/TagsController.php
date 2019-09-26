<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tags;
use Illuminate\Validation\Rule;

class TagsController extends Controller
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
        return view('Tags.index');
    }

    public function list(){

        $tags = Tags::where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $tags->previousPageUrl(),
                'next_page_url' => $tags->nextPageUrl(),
                'total'         => $tags->total(),
                'current_page'  => $tags->currentPage(),
                'per_page'      => $tags->perPage(),
                'last_page'     => $tags->lastPage(),
                'from'          => $tags->firstItem(),
                'to'            => $tags->lastPage(),
            ],
            'tags' => $tags,
        ]; 
        unset($tags);
        return response()->json($data, 200);
    }

    public function getstatus(Request $request, $id){
        $tags = tags::find($id);
        $tags->status = !$request->get('status');
        $tags->update();
        return;
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => ['required',
                Rule::unique('tags')->where(function ($query) {
                    return $query->where('deleted', 0);
                })
            ],
        ]);
        //
        Tags::create([
            'name'              => $request->get('name'),
            'description'       => $request->get('description') !=  "" ? $request->get('description') : "sin descripción.",
            'coordinators_id'   => \Auth::user()->Coordinators->id,
        ]);
        return;
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'required',
        ]);
        //
        $tags = Tags::find($id);
        $tags->name            = $request->get('name');
        $tags->description     = $request->get('description') !=  "" ? $request->get('description') : "sin descripción.";
        $tags->update();
        //
        return;
    }

    public function destroy($id){
        $tags = tags::find($id);
        $tags->deleted = time();
        if($tags->Taxts){
        	foreach ($tags->Taxts as $taxts) {
                $taxts->deleted = time();
                $taxts->update();
            }
        }
        $tags->update();
        return;
    }
}
