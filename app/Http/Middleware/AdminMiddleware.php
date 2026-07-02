<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware ini memastikan hanya user dengan role 'admin' yang bisa
 * mengakses route yang dilindungi (misal: /admin/*).
 * Jika bukan admin, akan di-redirect ke dashboard anggota.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
