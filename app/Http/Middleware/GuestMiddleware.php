<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = session('user_id');

        if ($userId && User::whereKey($userId)->exists()) {
            return redirect()->route('dashboard');
        }

        if ($userId) {
            $request->session()->flush();
        }

        return $next($request);
    }
}
