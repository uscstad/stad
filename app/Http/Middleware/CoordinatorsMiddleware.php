<?php

namespace App\Http\Middleware;

use Closure;

class CoordinatorsMiddleware
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
        /*if($request->session()->get('rol') != 'coordinators'){
            return redirect('/')->with(["message" => "El usuario no tiene los permisos correctos.", "code" => 403]);
        }*/
        return $next($request);
    }
}
