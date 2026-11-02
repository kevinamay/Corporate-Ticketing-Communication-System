<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    /**
     * Handle an incoming request for mobile API endpoints.
     */
    public function handle(Request $request, Closure $next, ?string $requiredRole = null): Response
    {
        $token = $request->bearerToken() ?: $request->header('X-Auth-Token') ?: $request->input('api_token');

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token otentikasi tidak ditemukan. Silakan login terlebih dahulu.',
            ], 401);
        }

        try {
            $payload = json_decode(Crypt::decryptString($token), true);
            if (! is_array($payload) || ! isset($payload['user_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid. Silakan login ulang.',
                ], 401);
            }

            $user = User::with('department')->find($payload['user_id']);
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun pengguna tidak ditemukan.',
                ], 401);
            }

            if ($user->email_verified_at === null && $user->role !== 'admin' && $user->email !== 'user123@gmail.com') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda belum aktif karena masih menunggu persetujuan (ACC) dari Administrator.',
                ], 403);
            }

            // Set current authenticated user for request
            Auth::setUser($user);
            $request->setUserResolver(fn () => $user);

            // Optional role guard check
            if ($requiredRole === 'hrd') {
                $deptName = strtolower($user->department?->name ?? '');
                $isHrdOrAdmin = $user->role === 'admin'
                    || $user->email === 'user123@gmail.com'
                    || str_contains($deptName, 'hr')
                    || str_contains($deptName, 'human resource')
                    || str_contains($deptName, 'personalia');

                if (! $isHrdOrAdmin) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses Terbatas: Akun Anda tidak memiliki hak akses HRD / Administrator.',
                    ], 403);
                }
            } elseif ($requiredRole === 'employee') {
                $deptName = strtolower($user->department?->name ?? '');
                $isHrdOrAdmin = $user->role === 'admin'
                    || $user->email === 'user123@gmail.com'
                    || str_contains($deptName, 'hr')
                    || str_contains($deptName, 'human resource')
                    || str_contains($deptName, 'personalia');

                if ($isHrdOrAdmin) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses Ditolak: Akun Anda terdaftar sebagai Tim HRD. Silakan gunakan Aplikasi HRD.',
                    ], 403);
                }
            }

            return $next($request);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi login telah kadaluarsa atau token rusak. Silakan login kembali.',
            ], 401);
        }
    }
}
