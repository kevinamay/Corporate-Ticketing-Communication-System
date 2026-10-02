<?php

namespace Tests\Feature;

use App\Livewire\TicketForm;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketFormAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Department $itDept;

    protected Department $hrDept;

    protected User $employeeUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itDept = Department::create([
            'id' => 1,
            'name' => 'IT Support',
            'icon' => 'laptop',
            'description' => 'IT Division',
        ]);

        $this->hrDept = Department::create([
            'id' => 2,
            'name' => 'HR Department',
            'icon' => 'users',
            'description' => 'HR Division',
        ]);

        $this->employeeUser = User::factory()->create([
            'name' => 'Kevina Maydiva',
            'email' => 'kevinamay23@gmail.com',
            'role' => 'staff',
            'department_id' => $this->hrDept->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_logged_in_employee_does_not_see_guest_warning_in_ticket_form(): void
    {
        $this->actingAs($this->employeeUser);
        session(['active_user_id' => $this->employeeUser->id]);

        Livewire::test(TicketForm::class)
            ->assertOk()
            ->assertDontSee('Akses Tamu (Belum Login)')
            ->assertDontSee('Masuk (Login) untuk Kirim Tiket')
            ->assertSee('Submit Request');
    }

    public function test_logged_in_employee_can_submit_ticket_successfully(): void
    {
        $this->actingAs($this->employeeUser);
        session(['active_user_id' => $this->employeeUser->id]);

        Livewire::test(TicketForm::class)
            ->set('title', 'KOMPUTER ERROR DI RUANG HCM')
            ->set('target_department_id', $this->itDept->id)
            ->set('sender_department_id', $this->hrDept->id)
            ->set('category', 'Hardware')
            ->set('priority', 'High')
            ->set('description', 'Komputer kantor mati total dan tidak bisa dinyalakan.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSuccess', true);

        $this->assertDatabaseHas('tickets', [
            'title' => 'KOMPUTER ERROR DI RUANG HCM',
            'user_id' => $this->employeeUser->id,
            'target_department_id' => $this->itDept->id,
            'status' => 'Pending',
        ]);
    }

    public function test_unauthenticated_user_is_redirected_away_from_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }
}
