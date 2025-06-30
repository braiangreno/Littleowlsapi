<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, \Closure $next, ...$guards)
    {
        if (Auth::guard($guards[0] ?? null)->check()) {
            // En vez de redirigir a dashboard, devolvemos 403 JSON para API
            return response()->json(['message' => 'Already authenticated.'], 403);
        }

        return $next($request);
    }
} 