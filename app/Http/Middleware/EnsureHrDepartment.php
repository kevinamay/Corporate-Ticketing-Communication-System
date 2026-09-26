<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHrDepartment
{
    /**
     * Handle an incoming request.
     * Ensure ONLY user123@gmail.com can access the Employee Master Data vault.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Akses Ditolak: Anda belum terotentikasi.');
        }

        if ($user->email !== 'user123@gmail.com') {
            abort(403, 'Akses Ditolak: Halaman Master Data Karyawan khusus dan hanya dapat diakses oleh Admin IT (user123@gmail.com).');
        }

        return $next($request);
    }
}
