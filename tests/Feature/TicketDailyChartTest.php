<?php

namespace Tests\Feature;

use App\Livewire\TicketDailyChart;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketDailyChartTest extends TestCase
{
    use RefreshDatabase;

    protected Department $itDept;

    protected Department $hrDept;

    protected User $adminUser;

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
            'name' => 'Admin IT',
            'icon' => 'shield-check',
            'description' => 'Admin IT Division',
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin System',
            'email' => 'user123@gmail.com',
            'password' => bcrypt('password123'),
            'department_id' => 1,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test chart renders properly with empty data (CSAT style No Data Recorded).
     */
    public function test_chart_renders_empty_state_correctly(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(TicketDailyChart::class)
            ->assertStatus(200)
            ->assertSee(__('Tren Permintaan Tiket Harian'))
            ->assertSee(__('No data recorded'))
            ->assertSee(__('0 Tiket'));
    }

    /**
     * Test chart aggregates daily ticket volume and metrics accurately.
     */
    public function test_chart_computes_daily_metrics_and_renders_tickets(): void
    {
        $this->actingAs($this->adminUser);

        // Create 3 tickets today
        Ticket::create([
            'sender_id' => $this->adminUser->id,
            'user_id' => $this->adminUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Komputer Error 1',
            'category' => 'Hardware',
            'description' => 'Monitor mati total',
            'priority' => 'High',
            'status' => 'Pending',
            'created_at' => Carbon::now(),
        ]);

        Ticket::create([
            'sender_id' => $this->adminUser->id,
            'user_id' => $this->adminUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Komputer Error 2',
            'category' => 'Hardware',
            'description' => 'Keyboard tidak responsif',
            'priority' => 'Medium',
            'status' => 'In Progress',
            'created_at' => Carbon::now(),
        ]);

        Ticket::create([
            'sender_id' => $this->adminUser->id,
            'user_id' => $this->adminUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Komputer Error 3',
            'category' => 'Hardware',
            'description' => 'Mouse rusak',
            'priority' => 'Low',
            'status' => 'Resolved',
            'created_at' => Carbon::now(),
        ]);

        // Create 1 ticket yesterday
        Ticket::create([
            'sender_id' => $this->adminUser->id,
            'user_id' => $this->adminUser->id,
            'target_department_id' => $this->hrDept->id,
            'title' => 'Pengajuan Slip Gaji',
            'category' => 'Payroll',
            'description' => 'Minta slip gaji bulan ini',
            'priority' => 'Medium',
            'status' => 'Resolved',
            'created_at' => Carbon::now()->subDay(),
        ]);

        Livewire::test(TicketDailyChart::class)
            ->assertStatus(200)
            ->assertDontSee(__('No data recorded'))
            ->assertSee('4') // totalCount = 4
            ->assertSee('2') // resolvedCount = 2
            ->assertSee('50%'); // resolutionRate = 50%
    }

    /**
     * Test switching timeframe and chart type.
     */
    public function test_timeframe_and_chart_type_switch(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(TicketDailyChart::class)
            ->call('setDays', 7)
            ->assertSet('days', 7)
            ->call('setDays', 30)
            ->assertSet('days', 30)
            ->call('setChartType', 'bar')
            ->assertSet('chartType', 'bar')
            ->call('setChartType', 'line')
            ->assertSet('chartType', 'line');
    }

    /**
     * Test department filtering.
     */
    public function test_department_filter(): void
    {
        $this->actingAs($this->adminUser);

        Ticket::create([
            'sender_id' => $this->adminUser->id,
            'user_id' => $this->adminUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'IT Ticket Only',
            'category' => 'Hardware',
            'description' => 'PC IT issue',
            'priority' => 'High',
            'status' => 'Pending',
            'created_at' => Carbon::now(),
        ]);

        Ticket::create([
            'sender_id' => $this->adminUser->id,
            'user_id' => $this->adminUser->id,
            'target_department_id' => $this->hrDept->id,
            'title' => 'HR Ticket Only',
            'category' => 'Payroll',
            'description' => 'HR issue',
            'priority' => 'Medium',
            'status' => 'Pending',
            'created_at' => Carbon::now(),
        ]);

        // Default all
        $test = Livewire::test(TicketDailyChart::class);
        $test->assertViewHas('totalCount', 2);

        // Filter to IT Dept
        $test->set('departmentFilter', (string) $this->itDept->id)
            ->assertViewHas('totalCount', 1);

        // Filter to HR Dept
        $test->set('departmentFilter', (string) $this->hrDept->id)
            ->assertViewHas('totalCount', 1);
    }

    /**
     * Test chart appears on the admin dashboard between employee master and global tickets.
     */
    public function test_chart_displayed_on_admin_dashboard(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('daily-ticket-chart-section');
        $response->assertSee(__('Tren Permintaan Tiket Harian'));
    }
}
