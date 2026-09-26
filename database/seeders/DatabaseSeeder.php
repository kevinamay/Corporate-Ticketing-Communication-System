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

        // 3. Buat Akun Pengguna Nyata per Divisi untuk Keperluan Uji Komunikasi Antar Divisi
        $password = Hash::make('password123');

        // Divisi HRD (Staff pengirim permohonan / tiket)
        User::updateOrCreate(
            ['email' => 'siti.hrd@asiaplastik.com'],
            [
                'name' => 'Siti Rahmawati',
                'password' => $password,
                'department_id' => $hrDept->id,
                'role' => 'staff',
                'national_id_ktp' => '3578015507940002',
                'whatsapp_number' => '081234567892',
                'gender' => 'female',
                'complete_address' => 'Jl. Rungkut Industri III No. 15, Surabaya',
                'postal_code' => '60293',
                'email_verified_at' => now(),
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150',
            ]
        );

        // Divisi IT Support (Agent penerima masalah teknis)
        User::updateOrCreate(
            ['email' => 'budi.it@asiaplastik.com'],
            [
                'name' => 'Budi Pratama',
                'password' => $password,
                'department_id' => $itDept->id,
                'role' => 'agent',
                'national_id_ktp' => '3578011203900001',
                'whatsapp_number' => '081234567891',
                'gender' => 'male',
                'complete_address' => 'Jl. Jemursari No. 42, Surabaya',
                'postal_code' => '60237',
                'email_verified_at' => now(),
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150',
            ]
        );

        // Divisi Maintenance & Fasilitas (Teknisi)
        User::updateOrCreate(
            ['email' => 'agus.teknisi@asiaplastik.com'],
            [
                'name' => 'Agus Santoso',
                'password' => $password,
                'department_id' => $maintDept->id,
                'role' => 'agent',
                'national_id_ktp' => '3578012408880003',
                'whatsapp_number' => '081234567893',
                'gender' => 'male',
                'complete_address' => 'Jl. Kendangsari Blok C No. 8, Surabaya',
                'postal_code' => '60292',
                'email_verified_at' => now(),
                'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=150',
            ]
        );

        // Divisi Produksi
        User::updateOrCreate(
            ['email' => 'hendra.produksi@asiaplastik.com'],
            [
                'name' => 'Hendra Wijaya',
                'password' => $password,
                'department_id' => $prodDept->id,
                'role' => 'staff',
                'national_id_ktp' => '3578011805850004',
                'whatsapp_number' => '081234567894',
                'gender' => 'male',
                'complete_address' => 'Jl. Kutisari Indah No. 20, Surabaya',
                'postal_code' => '60291',
                'email_verified_at' => now(),
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150',
            ]
        );

        // Administrator Sistem
        User::updateOrCreate(
            ['email' => 'admin@asiaplastik.com'],
            [
                'name' => 'Admin Sistem',
                'password' => $password,
                'department_id' => null,
                'role' => 'admin',
                'national_id_ktp' => '3578010101900000',
                'whatsapp_number' => '081234567890',
                'gender' => 'male',
                'complete_address' => 'Head Office PT Asia Plastik, Surabaya',
                'postal_code' => '60293',
                'email_verified_at' => now(),
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150',
            ]
        );

        // Admin IT Utama (user123@gmail.com / password123)
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

        // 4. Buat Master Data Karyawan (HCM Core Master Data)
        EmployeeMasterData::updateOrCreate(
            ['ktp_number' => '3578015507940002'],
            ['name' => 'Siti Rahmawati', 'department_id' => $hrDept->id]
        );

        EmployeeMasterData::updateOrCreate(
            ['ktp_number' => '3578011203900001'],
            ['name' => 'Budi Pratama', 'department_id' => $itDept->id]
        );

        EmployeeMasterData::updateOrCreate(
            ['ktp_number' => '3578012408880003'],
            ['name' => 'Agus Santoso', 'department_id' => $maintDept->id]
        );

        EmployeeMasterData::updateOrCreate(
            ['ktp_number' => '3578011805850004'],
            ['name' => 'Hendra Wijaya', 'department_id' => $prodDept->id]
        );

        EmployeeMasterData::updateOrCreate(
            ['ktp_number' => '3578016609950005'],
            ['name' => 'Dewi Lestari', 'department_id' => $logDept->id]
        );

        EmployeeMasterData::updateOrCreate(
            ['ktp_number' => '3578010502930006'],
            ['name' => 'Rian Hidayat', 'department_id' => $prodDept->id]
        );

        // Catatan: Tidak ada tiket dummy atau pesan dummy yang dibuat di sini.
        // Semua tiket dan pesan akan dibuat secara riil oleh pengguna selama pengujian.
    }
}
