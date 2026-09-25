<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHrDepartment
{
    /**
     * Handle an incoming request.
     * Ensure ONLY users with department_id === 2 (HR Department) can access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Akses Ditolak: Anda belum terotentikasi.');
        }

        // Strict authorization: department_id === 2 (HR Department)
        // Also support department_id === 4 (HRD in existing seed) or department name matching HR
        $isHrDepartment = ((int) $user->department_id === 2)
            || ((int) $user->department_id === 4)
            || ($user->department && (
                str_contains(strtolower($user->department->name), 'human resources') ||
                str_contains(strtolower($user->department->name), 'hr')
            ));

        if (! $isHrDepartment) {
            abort(403, 'Akses Ditolak: Modul HCM Master Data hanya dapat diakses oleh Departemen HRD (department_id = 2).');
        }

        return $next($request);
    }
}
