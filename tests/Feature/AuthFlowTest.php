<?php

namespace Tests\Feature;

use App\Livewire\HcmEmployeeMaster;
use App\Livewire\LoginUser;
use App\Livewire\RegisterUser;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_awaits_admin_acc_and_blocks_login_until_approved(): void
    {
        $dept = Department::firstOrCreate(
            ['name' => 'IT Support'],
            ['icon' => 'laptop', 'description' => 'IT Support Dept']
        );

        $admin = User::factory()->create([
            'name' => 'Admin IT',
            'email' => 'user123@gmail.com',
            'role' => 'admin',
            'department_id' => $dept->id,
            'email_verified_at' => now(),
        ]);

        $testEmail = 'test_'.uniqid().'@asiaplastik.com';
        $testPhone = '081234567890';

        // 1. Submit Registration Form without OTP
        $testComponent = Livewire::test(RegisterUser::class)
            ->set('name', 'Budi Santoso')
            ->set('whatsapp_number', $testPhone)
            ->set('email', $testEmail)
            ->set('password', 'secret12345')
            ->set('password_confirmation', 'secret12345')
            ->call('register');

        // Check user created in database with email_verified_at = null (pending Admin ACC)
        $user = User::where('email', $testEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('Budi Santoso', $user->name);
        $this->assertEquals($testEmail, $user->email);
        $this->assertEquals($testPhone, $user->whatsapp_number);
        $this->assertNull($user->otp_code);
        $this->assertNull($user->email_verified_at);

        // Component should show registered success state
        $testComponent->assertSet('isRegisteredSuccess', true);
        $testComponent->assertSee('Pendaftaran Berhasil Dikirim!');

        // 2. User tries to login before admin approval -> must be rejected
        Auth::logout();
        Livewire::test(LoginUser::class)
            ->set('login_id', $testEmail)
            ->set('password', 'secret12345')
            ->call('login')
            ->assertSet('errorMessage', 'Akun Anda belum aktif karena masih menunggu persetujuan (ACC/Konfirmasi) dari Administrator. Silakan hubungi Administrator untuk aktivasi akun.');

        $this->assertFalse(Auth::check());

        // 3. Admin ACC/Approves the employee in HcmEmployeeMaster
        $this->actingAs($admin);
        Livewire::test(HcmEmployeeMaster::class)
            ->call('toggleApproval', $user->id)
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);

        // 4. Now the employee can log in successfully
        Auth::logout();
        Livewire::test(LoginUser::class)
            ->set('login_id', $testEmail)
            ->set('password', 'secret12345')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_login_with_whatsapp_and_email(): void
    {
        $dept = Department::first();
        $ktp = '3578'.str_pad((string) random_int(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
        $email = 'login_'.uniqid().'@asiaplastik.com';
        $whatsapp = '081299998888';

        $user = User::create([
            'name' => 'Login Tester',
            'email' => $email,
            'password' => bcrypt('password123'),
            'national_id_ktp' => $ktp,
            'gender' => 'female',
            'whatsapp_number' => $whatsapp,
            'complete_address' => 'Surabaya, Jawa Timur',
            'postal_code' => '60111',
            'department_id' => $dept?->id,
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        // Test login with WhatsApp Number
        Auth::logout();
        Livewire::test(LoginUser::class)
            ->set('login_id', $whatsapp)
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());

        // Test login with Email
        Auth::logout();
        Livewire::test(LoginUser::class)
            ->set('login_id', $email)
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());

        // Test that login with KTP is rejected
        Auth::logout();
        Livewire::test(LoginUser::class)
            ->set('login_id', $ktp)
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertSet('errorMessage', 'Akun dengan Email atau No. WhatsApp tersebut belum terdaftar. Silakan lakukan registrasi terlebih dahulu.');
        $this->assertFalse(Auth::check());
    }
}
