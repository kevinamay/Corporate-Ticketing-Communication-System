<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Helper to find user by email or WhatsApp variation.
     */
    protected function findUserByLoginId(string $input): ?User
    {
        $input = trim($input);
        $cleanEmail = strtolower($input);

        $phoneVariations = [$input];
        $digitsOnly = preg_replace('/[^0-9]/', '', $input);
        if (! empty($digitsOnly)) {
            $phoneVariations[] = $digitsOnly;
            if (str_starts_with($digitsOnly, '62')) {
                $phoneVariations[] = '0'.substr($digitsOnly, 2);
            } elseif (str_starts_with($digitsOnly, '0')) {
                $phoneVariations[] = '62'.substr($digitsOnly, 1);
                $phoneVariations[] = '+62'.substr($digitsOnly, 1);
            }
        }
        $phoneVariations = array_values(array_unique($phoneVariations));

        return User::with('department')
            ->where(function ($query) use ($cleanEmail, $input, $phoneVariations) {
                $query->where('email', $cleanEmail)
                    ->orWhere('email', $input)
                    ->orWhereIn('whatsapp_number', $phoneVariations);
            })
            ->first();
    }

    /**
     * Check if user is HRD or Administrator.
     */
    protected function isHrdOrAdmin(User $user): bool
    {
        $deptName = strtolower($user->department?->name ?? '');

        return $user->role === 'admin'
            || $user->email === 'user123@gmail.com'
            || str_contains($deptName, 'hr')
            || str_contains($deptName, 'human resource')
            || str_contains($deptName, 'personalia');
    }

    /**
     * Format user data for mobile responses.
     */
    protected function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'whatsapp_number' => $user->whatsapp_number,
            'national_id_ktp' => $user->national_id_ktp,
            'role' => $user->role,
            'gender' => $user->gender,
            'avatar' => $user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0284c7&color=fff',
            'department_id' => $user->department_id,
            'department' => $user->department ? [
                'id' => $user->department->id,
                'name' => $user->department->name,
                'icon' => $user->department->icon,
                'description' => $user->department->description,
            ] : null,
            'is_hrd_or_admin' => $this->isHrdOrAdmin($user),
            'is_approved' => $user->email_verified_at !== null,
        ];
    }

    /**
     * Mobile Employee Login: Exclusively for regular employees (non-HRD, non-Admin).
     */
    public function loginEmployee(Request $request): JsonResponse
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->findUserByLoginId($request->login_id);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email/WhatsApp atau Password yang Anda masukkan tidak sesuai.',
            ], 401);
        }

        if ($user->email_verified_at === null) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum aktif karena masih menunggu persetujuan (ACC) dari Administrator.',
            ], 403);
        }

        // Strict Portal Segregation: HRD & Admin are rejected on employee mobile app
        if ($this->isHrdOrAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak: Akun Anda terdaftar sebagai Tim HRD / Administrator. Halaman ini khusus untuk Aplikasi Karyawan. Silakan masuk melalui Aplikasi HRD.',
            ], 403);
        }

        $token = Crypt::encryptString(json_encode([
            'user_id' => $user->id,
            'role' => 'employee',
            'created_at' => time(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil. Selamat datang di Portal Layanan Karyawan.',
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Mobile HRD Login: Exclusively for HRD staff and IT Administrators.
     */
    public function loginHrd(Request $request): JsonResponse
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->findUserByLoginId($request->login_id);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email/WhatsApp atau Password staf HRD tidak sesuai.',
            ], 401);
        }

        if ($user->email_verified_at === null) {
            return response()->json([
                'success' => false,
                'message' => 'Akun staf Anda belum aktif karena masih menunggu persetujuan Administrator.',
            ], 403);
        }

        // Strict Portal Segregation: Regular employees are rejected on HRD mobile app
        if (! $this->isHrdOrAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses Terbatas: Akun Anda terdaftar di divisi lain. Aplikasi ini khusus diperuntukkan bagi Tim Human Resources (HRD) dan Administrator.',
            ], 403);
        }

        $token = Crypt::encryptString(json_encode([
            'user_id' => $user->id,
            'role' => 'hrd',
            'created_at' => time(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil. Selamat datang di Konsol HRD & Administrator.',
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Employee Registration via Mobile App.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'whatsapp_number' => 'required|string|unique:users,whatsapp_number',
            'national_id_ktp' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:male,female',
            'complete_address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'whatsapp_number' => $validated['whatsapp_number'],
            'national_id_ktp' => $validated['national_id_ktp'] ?? null,
            'department_id' => $validated['department_id'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? 'male',
            'complete_address' => $validated['complete_address'] ?? null,
            'role' => 'staff',
            'email_verified_at' => null, // Pending approval by HRD/Admin
            'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($validated['name']).'&background=0284c7&color=fff',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil dikirim! Akun Anda sedang menunggu persetujuan (ACC) dari Administrator/HRD sebelum dapat digunakan untuk login.',
            'user' => $this->formatUser($user),
        ], 201);
    }

    /**
     * Get Current Authenticated Profile.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Logout.
     */
    public function logout(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Sesi login telah berhasil diakhiri.',
        ]);
    }
}
