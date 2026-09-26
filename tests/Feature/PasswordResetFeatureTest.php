<?php

namespace Tests\Feature;

use App\Livewire\ForgotPassword;
use App\Livewire\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSeeLivewire('forgot-password');
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@asiaplastik.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'employee@asiaplastik.com')
            ->call('sendResetLink')
            ->assertSet('isSent', true)
            ->assertSet('errorMessage', null);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'employee@asiaplastik.com',
        ]);
    }

    public function test_cannot_request_reset_link_with_unregistered_email(): void
    {
        $test = Livewire::test(ForgotPassword::class)
            ->set('email', 'unknown@nonexistent.com')
            ->call('sendResetLink')
            ->assertSet('isSent', false);

        $this->assertNotNull($test->get('errorMessage'));
    }

    public function test_reset_password_page_can_be_rendered_with_token(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@asiaplastik.com',
        ]);

        $token = 'test-valid-reset-token-64chars';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $response = $this->get("/reset-password/{$token}?email=".urlencode($user->email));
        $response->assertStatus(200);
        $response->assertSeeLivewire('reset-password');
    }

    public function test_user_can_reset_password_and_redirect_to_login(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@asiaplastik.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = 'test-token-abcdef123456';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'BrandNewPassword123!')
            ->set('password_confirmation', 'BrandNewPassword123!')
            ->call('resetPassword')
            ->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('BrandNewPassword123!', $user->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_cannot_reset_password_with_mismatched_confirmation(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@asiaplastik.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = 'test-token-abcdef123456';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'BrandNewPassword123!')
            ->set('password_confirmation', 'DifferentPassword!')
            ->call('resetPassword')
            ->assertHasErrors(['password' => 'confirmed']);
    }
}
