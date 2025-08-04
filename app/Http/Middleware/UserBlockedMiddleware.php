<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserBlockedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user()->role == 'user' && Auth::user()->is_blocked == '1'){
            return redirect()->route('suspend', ['token'=>Hash("md5", Auth::user()->name)])->with('error', 'Your account has been blocked by administrator!');
        }

        return $next($request);
    }
}
