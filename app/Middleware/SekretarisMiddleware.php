<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SekretarisMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user memiliki role 'sekretaris'
        if (auth()->check() && auth()->user()->role === 'sekretaris') {
            return $next($request);
        }

        // Jika bukan sekretaris, arahkan ke halaman login
        return redirect()->route('login');
    }
}
