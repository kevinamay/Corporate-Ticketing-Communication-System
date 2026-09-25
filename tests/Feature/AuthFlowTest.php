<?php

namespace Tests\Feature;

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

    public function test_registration_and_otp_verification_flow(): void
    {
        $dept = Department::firstOrCreate(
            ['name' => 'IT Support'],
            ['icon' => 'laptop', 'description' => 'IT Support Dept']
        );

        $testEmail = 'test_'.uniqid().'@asiaplastik.com';

        // 1. Submit Registration Form with 5 required fields
        $testComponent = Livewire::test(RegisterUser::class)
            ->set('name', 'Budi Santoso')
            ->set('whatsapp_number', '081234567890')
            ->set('email', $testEmail)
            ->set('password', 'secret12345')
            ->set('password_confirmation', 'secret12345')
            ->call('register');

        // Check user created in database with the specified fields
        $user = User::where('email', $testEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('Budi Santoso', $user->name);
        $this->assertEquals($testEmail, $user->email);
        $this->assertEquals('081234567890', $user->whatsapp_number);
        $this->assertNotNull($user->avatar);
        $this->assertNotNull($user->otp_code);
        $this->assertEquals(6, strlen($user->otp_code));
        $this->assertNull($user->email_verified_at);

        // 2. Component transitioned to step 2 (OTP)
        $testComponent->assertSet('step', 2);
        $generatedOtp = $user->otp_code;

        // 3. Test Invalid OTP
        $testComponent
            ->set('otp1', '9')
            ->set('otp2', '9')
            ->set('otp3', '9')
            ->set('otp4', '9')
            ->set('otp5', '9')
            ->set('otp6', '9')
            ->call('verifyOtp')
            ->assertSee('Kode OTP tidak cocok');

        // 4. Test Valid OTP verification
        $testComponent
            ->set('otp1', $generatedOtp[0])
            ->set('otp2', $generatedOtp[1])
            ->set('otp3', $generatedOtp[2])
            ->set('otp4', $generatedOtp[3])
            ->set('otp5', $generatedOtp[4])
            ->set('otp6', $generatedOtp[5])
            ->call('verifyOtp')
            ->assertRedirect(route('login'));

        // Refresh user from DB to verify verified status
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->otp_code);

        // Verify redirected back to login without auto-login per user requirement
        $this->assertFalse(Auth::check());
    }

    public function test_login_with_ktp_and_email(): void
    {
        $dept = Department::first();
        $ktp = '3578'.str_pad((string) random_int(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
        $email = 'login_'.uniqid().'@asiaplastik.com';

        $user = User::create([
            'name' => 'Login Tester',
            'email' => $email,
            'password' => bcrypt('password123'),
            'national_id_ktp' => $ktp,
            'gender' => 'female',
            'whatsapp_number' => '081299998888',
            'complete_address' => 'Surabaya, Jawa Timur',
            'postal_code' => '60111',
            'department_id' => $dept?->id,
            'role' => 'staff',
        ]);

        // Test login with KTP
        Auth::logout();
        Livewire::test(LoginUser::class)
            ->set('login_id', $ktp)
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
    }
}
