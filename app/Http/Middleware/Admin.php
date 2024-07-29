<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!session()->has('auth')) {
            session(['message'=>'You are Unauthorized! Please login.', 'type'=>'warning']);

            return redirect('/auth/login');
        }else{
            if(session('position_name') == "Customer"){
                session(['message'=>'You are Unauthorized!', 'type'=>'warning']);

                return redirect('/ecommerce/shop');
            }
        }
        return $next($request);
    }
}



