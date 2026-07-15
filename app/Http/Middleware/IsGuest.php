<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return redirect('/database')->with('guest', 'Kamu sudah login');
        }

        return $next($request);
    }
}
