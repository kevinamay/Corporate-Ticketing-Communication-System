<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected Department $itDept;

    protected Department $hrDept;

    protected User $employee;

    protected User $hrStaff;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itDept = Department::create([
            'name' => 'IT Support',
            'icon' => 'laptop',
            'description' => 'IT Support Division',
        ]);

        $this->hrDept = Department::create([
            'name' => 'Human Resources (HRD)',
            'icon' => 'users',
            'description' => 'HR Division',
        ]);

        $this->employee = User::create([
            'name' => 'Kevina Maydiva',
            'email' => 'kevinamay23@gmail.com',
            'whatsapp_number' => '082244109503',
            'password' => Hash::make('password123'),
            'department_id' => $this->itDept->id,
            'role' => 'employee',
            'email_verified_at' => now(),
        ]);

        $this->hrStaff = User::create([
            'name' => 'Staf HRD',
            'email' => 'hr@asiaplastik.com',
            'whatsapp_number' => '081234567800',
            'password' => Hash::make('password123'),
            'department_id' => $this->hrDept->id,
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'name' => 'Admin IT',
            'email' => 'user123@gmail.com',
            'whatsapp_number' => '081234567899',
            'password' => Hash::make('password123'),
            'department_id' => $this->itDept->id,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_public_metadata_endpoints(): void
    {
        $response = $this->getJson('/api/departments');
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'departments');

        $responseCat = $this->getJson('/api/categories');
        $responseCat->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_employee_can_login_via_employee_api_and_hrd_is_blocked(): void
    {
        // 1. Employee login -> 200 OK
        $response = $this->postJson('/api/auth/login-employee', [
            'login_id' => 'kevinamay23@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['token', 'user']);

        // 2. HRD staff login on employee API -> 403 Forbidden
        $responseHrd = $this->postJson('/api/auth/login-employee', [
            'login_id' => 'hr@asiaplastik.com',
            'password' => 'password123',
        ]);

        $responseHrd->assertStatus(403)
            ->assertJsonPath('success', false);

        // 3. Admin login on employee API -> 403 Forbidden
        $responseAdmin = $this->postJson('/api/auth/login-employee', [
            'login_id' => 'user123@gmail.com',
            'password' => 'password123',
        ]);

        $responseAdmin->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_hrd_can_login_via_hrd_api_and_employee_is_blocked(): void
    {
        // 1. HR staff login -> 200 OK
        $response = $this->postJson('/api/auth/login-hrd', [
            'login_id' => 'hr@asiaplastik.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['token', 'user']);

        // 2. Admin login -> 200 OK
        $responseAdmin = $this->postJson('/api/auth/login-hrd', [
            'login_id' => 'user123@gmail.com',
            'password' => 'password123',
        ]);

        $responseAdmin->assertStatus(200)
            ->assertJsonPath('success', true);

        // 3. Regular employee on HRD API -> 403 Forbidden
        $responseEmp = $this->postJson('/api/auth/login-hrd', [
            'login_id' => 'kevinamay23@gmail.com',
            'password' => 'password123',
        ]);

        $responseEmp->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_employee_ticket_workflow_via_api(): void
    {
        $empToken = Crypt::encryptString(json_encode(['user_id' => $this->employee->id]));

        // 1. Submit ticket
        $responseCreate = $this->withHeader('Authorization', 'Bearer '.$empToken)
            ->postJson('/api/employee/tickets', [
                'title' => 'Kendala Jaringan Printer Pabrik',
                'target_department_id' => $this->itDept->id,
                'category' => 'Hardware & PC',
                'priority' => 'High',
                'description' => 'Printer line 2 offline dan tidak dapat mencetak label pengemasan.',
            ]);

        $responseCreate->assertStatus(201)
            ->assertJsonPath('success', true);

        $ticketId = $responseCreate->json('ticket.id');

        // 2. Get employee tickets list
        $responseList = $this->withHeader('Authorization', 'Bearer '.$empToken)
            ->getJson('/api/employee/tickets');

        $responseList->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'tickets');

        // 3. Add message to ticket
        $responseMsg = $this->withHeader('Authorization', 'Bearer '.$empToken)
            ->postJson("/api/tickets/{$ticketId}/messages", [
                'message' => 'Sudah dicoba restart kabel LAN, tetap tidak menyala indikatornya.',
            ]);

        $responseMsg->assertStatus(201)
            ->assertJsonPath('success', true);

        // 4. View ticket details and messages thread
        $responseDetail = $this->withHeader('Authorization', 'Bearer '.$empToken)
            ->getJson("/api/tickets/{$ticketId}");

        $responseDetail->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'messages');
    }

    public function test_hrd_workflow_via_api(): void
    {
        $hrToken = Crypt::encryptString(json_encode(['user_id' => $this->hrStaff->id]));

        // Create a ticket to view
        $ticket = Ticket::create([
            'user_id' => $this->employee->id,
            'sender_id' => $this->employee->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Masalah Kompresor Angin',
            'category' => 'Facility & Maintenance',
            'priority' => 'Urgent',
            'description' => 'Tekanan angin menurun drastis pada blok C.',
            'status' => 'Pending',
        ]);

        // 1. HRD metrics
        $responseMetrics = $this->withHeader('Authorization', 'Bearer '.$hrToken)
            ->getJson('/api/hrd/metrics');

        $responseMetrics->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('metrics.total_tickets', 1);

        // 2. HRD global tickets list
        $responseTickets = $this->withHeader('Authorization', 'Bearer '.$hrToken)
            ->getJson('/api/hrd/tickets');

        $responseTickets->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'tickets');

        // 3. Update ticket status to In Progress
        $responseUpdate = $this->withHeader('Authorization', 'Bearer '.$hrToken)
            ->patchJson("/api/hrd/tickets/{$ticket->id}/status", [
                'status' => 'In Progress',
                'note' => 'Tim teknisi sedang dalam perjalanan ke lokasi.',
            ]);

        $responseUpdate->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('ticket.status', 'In Progress');

        // 4. Employee management list
        $responseEmps = $this->withHeader('Authorization', 'Bearer '.$hrToken)
            ->getJson('/api/hrd/employees');

        $responseEmps->assertStatus(200)
            ->assertJsonPath('success', true);

        // 5. Toggle employee approval
        $responseToggle = $this->withHeader('Authorization', 'Bearer '.$hrToken)
            ->postJson("/api/hrd/employees/{$this->employee->id}/toggle-approval");

        $responseToggle->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_approved', false);
    }
}
