<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KepalaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user yang login memiliki role kepala
        if (auth()->check() && auth()->user()->role === 'kepala') {
            return $next($request);
        }

        // Jika user login tapi bukan kepala, beri 403
        if (auth()->check()) {
            abort(403, 'Akses ditolak: hanya Kepala Desa yang boleh mengakses.');
        }

        // Jika belum login, redirect ke login
        return redirect()->route('login');
    }

}