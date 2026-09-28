<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\EmployeeMasterData;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with real divisions and corporate users.
     * Tickets and messages are kept clean (0) so the user can test real inter-division communication.
     */
    public function run(): void
    {
        // 1. Bersihkan tiket dan pesan lama agar database benar-benar bersih untuk pengujian nyata
        Message::query()->delete();
        Ticket::query()->delete();

        // 2. Buat Divisi / Departemen Resmi PT Asia Plastik
        $itDept = Department::updateOrCreate(
            ['name' => 'IT Support'],
            [
                'icon' => 'laptop',
                'description' => 'Bantuan teknis komputer, jaringan pabrik, printer, dan sistem informasi korporat',
            ]
        );

        $hrDept = Department::updateOrCreate(
            ['name' => 'Human Resources (HRD)'],
            [
                'icon' => 'users',
                'description' => 'Personalia, absensi, izin cuti, payroll karyawan, dan rekrutmen',
            ]
        );

        $maintDept = Department::updateOrCreate(
            ['name' => 'Facility & Maintenance'],
            [
                'icon' => 'wrench',
                'description' => 'Pemeliharaan fasilitas gedung, kelistrikan pabrik, AC, dan utilitas',
            ]
        );

        $prodDept = Department::updateOrCreate(
            ['name' => 'Produksi & Mesin'],
            [
                'icon' => 'cog',
                'description' => 'Operasional mesin injection molding, blow molding, dan lantai produksi',
            ]
        );

        $logDept = Department::updateOrCreate(
            ['name' => 'Gudang & Logistik'],
            [
                'icon' => 'archive',
                'description' => 'Penyimpanan bahan baku biji plastik, barang jadi, dan pengiriman kargo',
            ]
        );

        // 3. Akun Administrator IT Utama (user123@gmail.com / password123)
        $password = Hash::make('password123');

        User::updateOrCreate(
            ['email' => 'user123@gmail.com'],
            [
                'name' => 'Admin IT',
                'password' => $password,
                'department_id' => $itDept->id,
                'role' => 'admin',
                'national_id_ktp' => '3578010101990001',
                'whatsapp_number' => '081234567899',
                'gender' => 'male',
                'complete_address' => 'Head Office PT Asia Plastik, Surabaya',
                'postal_code' => '60293',
                'email_verified_at' => now(),
                'avatar' => 'https://ui-avatars.com/api/?name=Admin+IT&background=0284c7&color=fff',
            ]
        );

        // 4. Master Data Karyawan Resmi (HCM Core)
        EmployeeMasterData::updateOrCreate(
            ['ktp_number' => '3578010101990001'],
            ['name' => 'Admin IT', 'department_id' => $itDept->id]
        );

        EmployeeMasterData::updateOrCreate(
            ['ktp_number' => '3502016305030001'],
            ['name' => 'Kevina Maydiva Heriansaputri', 'department_id' => $itDept->id]
        );
    }
}
