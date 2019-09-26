<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */     
    public function handle($request, Closure $next)
    {
        /*if($request->session()->get('rol') != 'admins'){
            return redirect('/')->with(["message" => "El usuario no tiene los permisos correctos.", "code" => 403]);
        }*/     
        return $next($request);
    }
}
