<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOwner
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user memiliki role 'owner'
        if (Auth::check() && Auth::user()->role !== 'owner') {
            return redirect()->route('dashboard')->with('error', 'Akses hanya untuk owner.');
        }

        return $next($request);
    }
}

