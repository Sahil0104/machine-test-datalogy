<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('user_id')) {
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}
