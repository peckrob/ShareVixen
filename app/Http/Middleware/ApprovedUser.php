<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Support\Facades\Auth;

class ApprovedUser
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
        if (Auth::user()->approved == 0) {
            Auth::logout();

            Session::flash("message", 'Your account is still waiting for approval.');

            return redirect('login');
        }

        return $next($request);
    }
}
