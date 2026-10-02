<?php

namespace Tests\Feature;

use App\Livewire\ResolvedTicketHistory;
use App\Models\Department;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResolvedTicketHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected Department $itDept;

    protected Department $hrDept;

    protected User $adminUser;

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

        $this->adminUser = User::factory()->create([
            'name' => 'Admin IT',
            'email' => 'user123@gmail.com',
            'role' => 'admin',
            'department_id' => $this->itDept->id,
        ]);

        $this->employeeUser = User::factory()->create([
            'name' => 'Kevina Maydiva',
            'email' => 'kevina@example.com',
            'role' => 'employee',
            'department_id' => $this->hrDept->id,
        ]);
    }

    public function test_resolved_tickets_history_renders_for_admin_view(): void
    {
        $resolvedTicket = Ticket::create([
            'user_id' => $this->employeeUser->id,
            'sender_id' => $this->employeeUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Komputer Mati Total Ruang HR',
            'category' => 'Hardware',
            'description' => 'Power supply rusak dan perlu diganti.',
            'priority' => 'High',
            'status' => 'Resolved',
        ]);

        $pendingTicket = Ticket::create([
            'user_id' => $this->employeeUser->id,
            'sender_id' => $this->employeeUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Wi-Fi Ruang Meeting Lambat',
            'category' => 'Network',
            'description' => 'Koneksi lemot saat presentasi.',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(ResolvedTicketHistory::class, ['viewMode' => 'admin'])
            ->assertOk()
            ->assertSee('Komputer Mati Total Ruang HR')
            ->assertSee('Resolved')
            ->assertDontSee('Wi-Fi Ruang Meeting Lambat');
    }

    public function test_resolved_tickets_history_renders_for_employee_view(): void
    {
        $myResolved = Ticket::create([
            'user_id' => $this->employeeUser->id,
            'sender_id' => $this->employeeUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Printer Paper Jam HR',
            'category' => 'Hardware',
            'description' => 'Kertas tersangkut di roller printer.',
            'priority' => 'Medium',
            'status' => 'Resolved',
        ]);

        Message::create([
            'ticket_id' => $myResolved->id,
            'user_id' => $this->adminUser->id,
            'message' => 'Roller sudah dibersihkan dan dicoba cetak 5 lembar normal.',
        ]);

        $this->actingAs($this->employeeUser);

        Livewire::test(ResolvedTicketHistory::class, ['viewMode' => 'employee'])
            ->assertOk()
            ->assertSee('Printer Paper Jam HR')
            ->assertSee('Roller sudah dibersihkan')
            ->call('viewDetail', $myResolved->id)
            ->assertSet('isDetailModalOpen', true)
            ->assertSet('viewingTicketId', $myResolved->id)
            ->assertSee('Kertas tersangkut di roller printer')
            ->call('closeDetailModal')
            ->assertSet('isDetailModalOpen', false);
    }

    public function test_resolved_tickets_history_search_and_filter(): void
    {
        Ticket::create([
            'user_id' => $this->employeeUser->id,
            'sender_id' => $this->employeeUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Email Outlook Error',
            'category' => 'Software',
            'description' => 'Tidak bisa kirim email ke vendor.',
            'priority' => 'Medium',
            'status' => 'Resolved',
        ]);

        Ticket::create([
            'user_id' => $this->employeeUser->id,
            'sender_id' => $this->employeeUser->id,
            'target_department_id' => $this->hrDept->id,
            'title' => 'Slip Gaji Bulan September',
            'category' => 'Payroll',
            'description' => 'Konfirmasi perhitungan lembur.',
            'priority' => 'Low',
            'status' => 'Resolved',
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(ResolvedTicketHistory::class, ['viewMode' => 'admin'])
            ->set('search', 'Outlook')
            ->assertSee('Email Outlook Error')
            ->assertDontSee('Slip Gaji Bulan September')
            ->set('search', '')
            ->set('departmentFilter', (string) $this->hrDept->id)
            ->assertSee('Slip Gaji Bulan September')
            ->assertDontSee('Email Outlook Error');
    }
}
