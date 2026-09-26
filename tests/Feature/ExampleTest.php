<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login_and_login_page_renders(): void
    {
        $this->seed();
        $response = $this->get('/');
        $response->assertRedirect(route('login'));

        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Selamat Datang');
        $loginResponse->assertSee('Masuk ke Sistem');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $this->seed();
        $user = User::first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('ASIA');
        $response->assertSee('PERUSAHAAN');
    }
}
