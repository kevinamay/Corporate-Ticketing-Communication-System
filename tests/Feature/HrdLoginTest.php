<?php

namespace Tests\Feature;

use App\Livewire\LoginHrd;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class HrdLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_hrd_login_page_renders_successfully(): void
    {
        $response = $this->get('/hrd/login');

        $response->assertStatus(200);
        $response->assertSeeLivewire(LoginHrd::class);
        $response->assertSee('Portal HRD');
        $response->assertSee('Tim HRD');
    }

    public function test_hrd_shortcut_redirects_to_hrd_login(): void
    {
        $response = $this->get('/hrd');

        $response->assertRedirect(route('hrd.login'));
    }

    public function test_admin_can_login_via_hrd_portal(): void
    {
        $dept = Department::create(['name' => 'IT Support']);
        $admin = User::create([
            'name' => 'Admin IT',
            'email' => 'user123@gmail.com',
            'password' => Hash::make('password123'),
            'department_id' => $dept->id,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Livewire::test(LoginHrd::class)
            ->set('login_id', 'user123@gmail.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_hr_staff_can_login_via_hrd_portal(): void
    {
        $hrDept = Department::create(['name' => 'Human Resources (HRD)']);
        $hrStaff = User::create([
            'name' => 'HR Staff',
            'email' => 'hr@asiaplastik.com',
            'password' => Hash::make('password123'),
            'department_id' => $hrDept->id,
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        Livewire::test(LoginHrd::class)
            ->set('login_id', 'hr@asiaplastik.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($hrStaff);
    }

    public function test_non_hr_employee_is_blocked_from_hrd_portal(): void
    {
        $prodDept = Department::create(['name' => 'Produksi & Mesin']);
        $employee = User::create([
            'name' => 'Operator Produksi',
            'email' => 'op@asiaplastik.com',
            'password' => Hash::make('password123'),
            'department_id' => $prodDept->id,
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        Livewire::test(LoginHrd::class)
            ->set('login_id', 'op@asiaplastik.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertSet('errorMessage', 'Akses Terbatas: Akun Anda terdaftar di divisi lain. Halaman ini khusus diperuntukkan bagi Tim Human Resources (HRD) dan Administrator. Silakan gunakan Portal Karyawan.');

        $this->assertGuest();
    }

    public function test_unapproved_hr_user_cannot_login(): void
    {
        $hrDept = Department::create(['name' => 'Human Resources (HRD)']);
        User::create([
            'name' => 'HR Pending',
            'email' => 'pending.hr@asiaplastik.com',
            'password' => Hash::make('password123'),
            'department_id' => $hrDept->id,
            'role' => 'user',
            'email_verified_at' => null,
        ]);

        Livewire::test(LoginHrd::class)
            ->set('login_id', 'pending.hr@asiaplastik.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertSet('errorMessage', 'Akun Anda belum aktif karena masih menunggu persetujuan (ACC/Konfirmasi) dari Administrator. Silakan hubungi Administrator IT untuk aktivasi akun.');

        $this->assertGuest();
    }

    public function test_hrd_login_page_renders_in_chinese(): void
    {
        $response = $this->withCookie('locale', 'zh')->get('/hrd/login');

        $response->assertStatus(200);
        $response->assertSee('欢迎，人力资源团队');
        $response->assertSee('人力资源与人事部门集中认证');
        $response->assertSee('登录 HRD 门户');
    }

    public function test_hrd_login_page_renders_in_english(): void
    {
        $response = $this->withCookie('locale', 'en')->get('/hrd/login');

        $response->assertStatus(200);
        $response->assertSee('Welcome, HRD Team');
        $response->assertSee('Centralized Authentication for Human Resources & Personnel Division');
        $response->assertSee('Sign In to HRD Portal');
    }
}
