<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function change(Request $request, $name){
        $request->session()->get('rol');
        $request->session()->put('rol', $name); 
        return redirect('/');
    }

    public function redirect(){
        return redirect('/');
    }
}
