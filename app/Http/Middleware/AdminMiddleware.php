<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    # Function to check Admin is logged in or not, if Logged in U user_type = 1, Both are true give access, else Do login - 16/06/2021
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && Auth::user()->user_type == 1) {
            return $next($request);
        } else {
            return Redirect::to('admin/login')->with('error', 'You dont have Previlege to Access');
        }
    }
}
