<?php

namespace App\Http\Middleware;
use App\Models\Auth;
use Closure;
use Illuminate\Http\Request;

class AuthJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check($request->get('Token'))) {
            return $next($request);
        }

        $res = ['Cod'=>'error','Message'=>'Error de autenticación'];
        return response()->json($res,400);

    }
}
