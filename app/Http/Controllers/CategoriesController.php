<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categories;
use Illuminate\Validation\Rule;

class CategoriesController extends Controller
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
        return view('categories.index');
    }

    public function list(){

        $categories = Categories::where('coordinators_id',\Auth::user()->Coordinators->id)->where('deleted',0)->orderBy('id', 'desc')->paginate(15);
        $data = [
            'pagination' => [
                'prev_page_url' => $categories->previousPageUrl(),
                'next_page_url' => $categories->nextPageUrl(),
                'total'         => $categories->total(),
                'current_page'  => $categories->currentPage(),
                'per_page'      => $categories->perPage(),
                'last_page'     => $categories->lastPage(),
                'from'          => $categories->firstItem(),
                'to'            => $categories->lastPage(),
            ],
            'categories' => $categories,
        ]; 
        unset($categories);
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => ['required',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('deleted', 0);
                })
            ],
        ]);
        //
        Categories::create([
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
        $categories = Categories::find($id);
        $categories->name            = $request->get('name');
        $categories->description     = $request->get('description') !=  "" ? $request->get('description') : "sin descripción.";
        $categories->update();
        //
        return;
    }

    public function destroy($id){
        $categories = Categories::find($id);
        $categories->deleted = time();
        $categories->update();
        return;
    }
}
