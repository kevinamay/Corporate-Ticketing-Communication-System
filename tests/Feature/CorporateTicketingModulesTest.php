<?php

namespace Tests\Feature;

use App\Livewire\GlobalTickets;
use App\Livewire\HcmEmployeeMaster;
use App\Livewire\RegisterUser;
use App\Models\Department;
use App\Models\EmployeeMasterData;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class CorporateTicketingModulesTest extends TestCase
{
    use RefreshDatabase;

    protected Department $itDept;

    protected Department $hrDept;

    protected Department $prodDept;

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

        $this->prodDept = Department::create([
            'id' => 3,
            'name' => 'Produksi & Mesin',
            'icon' => 'cog',
            'description' => 'Production Division',
        ]);
    }

    /**
     * MODULE 1: Database & Migrations Incremental Additions Verification.
     */
    public function test_module_1_database_tables_and_columns_exist(): void
    {
        // 1. employee_master_data table
        $this->assertTrue(Schema::hasTable('employee_master_data'));
        $this->assertTrue(Schema::hasColumn('employee_master_data', 'ktp_number'));
        $this->assertTrue(Schema::hasColumn('employee_master_data', 'name'));
        $this->assertTrue(Schema::hasColumn('employee_master_data', 'department_id'));

        // 2. users table additions
        $this->assertTrue(Schema::hasColumn('users', 'ktp_number'));
        $this->assertTrue(Schema::hasColumn('users', 'department_id'));

        // 3. tickets table additions
        $this->assertTrue(Schema::hasColumn('tickets', 'target_department_id'));
        $this->assertTrue(Schema::hasColumn('tickets', 'category'));
    }

    /**
     * MODULE 2: Gatekeeper Aborts if KTP does NOT exist in employee_master_data.
     */
    public function test_module_2_registration_aborts_when_ktp_not_in_employee_master(): void
    {
        $unregisteredKtp = '3578999988887777';

        Livewire::test(RegisterUser::class)
            ->set('name', 'Calon Karyawan Liar')
            ->set('email', 'unregistered@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('national_id_ktp', $unregisteredKtp)
            ->set('gender', 'male')
            ->set('whatsapp_number', '081234567890')
            ->set('complete_address', 'Jl. Tanpa Arah No. 1')
            ->set('postal_code', '60293')
            ->call('register')
            ->assertHasErrors(['national_id_ktp'])
            ->assertSee('NIK/KTP tidak terdaftar di sistem HRD.');

        $this->assertDatabaseMissing('users', [
            'email' => 'unregistered@example.com',
        ]);
    }

    /**
     * MODULE 2: Gatekeeper succeeds and auto-assigns department_id from employee_master_data.
     */
    public function test_module_2_registration_succeeds_and_auto_assigns_department(): void
    {
        $validKtp = '3578015507940002';

        // Pre-register in HRD Master Data with Production Department (dept_id = 3)
        EmployeeMasterData::create([
            'ktp_number' => $validKtp,
            'name' => 'Karyawan Resmi Pabrik',
            'department_id' => $this->prodDept->id,
        ]);

        // Even if user selected IT Support (dept_id = 1), HRD Gatekeeper must auto-assign to Production (dept_id = 3)
        Livewire::test(RegisterUser::class)
            ->set('name', 'Karyawan Resmi Pabrik')
            ->set('email', 'resmi@asiaplastik.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('national_id_ktp', $validKtp)
            ->set('gender', 'male')
            ->set('whatsapp_number', '081234567890')
            ->set('complete_address', 'Jl. Industri Rungkut No. 55')
            ->set('postal_code', '60293')
            ->set('department_id', $this->itDept->id) // Will be overridden by HRD Gatekeeper
            ->call('register')
            ->assertHasNoErrors()
            ->assertSet('step', 2);

        $createdUser = User::where('national_id_ktp', $validKtp)->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals($validKtp, $createdUser->ktp_number);
        // Department MUST match employee_master_data (Production dept id = 3)
        $this->assertEquals($this->prodDept->id, $createdUser->department_id);
    }

    /**
     * MODULE 3: Security & Routing for /hcm-core/employees-master
     * Only users with department_id === 2 (HR) can access it.
     */
    public function test_module_3_hcm_vault_route_authorization(): void
    {
        // 1. Guest is redirected to login
        $response = $this->get('/hcm-core/employees-master');
        $response->assertRedirect('/login');

        // 2. Non-HR user (IT department_id = 1) is aborted with 403 Forbidden
        $itUser = User::create([
            'name' => 'Staff IT',
            'email' => 'it@asiaplastik.com',
            'password' => bcrypt('password123'),
            'department_id' => $this->itDept->id,
            'role' => 'staff',
        ]);

        $this->actingAs($itUser);
        session(['auth.password_confirmed_at' => time()]);

        $response = $this->get('/hcm-core/employees-master');
        $response->assertStatus(403);

        // 3. Admin IT user (user123@gmail.com) gets 200 OK
        $hrUser = User::create([
            'name' => 'Admin IT',
            'email' => 'user123@gmail.com',
            'password' => bcrypt('password123'),
            'department_id' => $this->hrDept->id,
            'role' => 'admin',
        ]);

        $this->actingAs($hrUser);
        session(['auth.password_confirmed_at' => time()]);

        $response = $this->get('/hcm-core/employees-master');
        $response->assertStatus(200);
        $response->assertSee('HCM Employee Master Data');
    }

    /**
     * MODULE 3: Manual CRUD and CSV Bulk Upload in HcmEmployeeMaster component.
     */
    public function test_module_3_manual_crud_and_csv_bulk_upload(): void
    {
        $hrUser = User::create([
            'name' => 'Admin IT',
            'email' => 'user123@gmail.com',
            'password' => bcrypt('password123'),
            'department_id' => $this->hrDept->id,
            'role' => 'admin',
        ]);

        $this->actingAs($hrUser);

        // 1. Manual Add Employee
        Livewire::test(HcmEmployeeMaster::class)
            ->call('openCreateModal')
            ->set('name', 'Ahmad Dani')
            ->set('whatsapp_number', '081234567801')
            ->set('email', 'ahmad.dani@asiaplastik.com')
            ->set('department_id', $this->itDept->id)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Ahmad Dani',
            'email' => 'ahmad.dani@asiaplastik.com',
            'whatsapp_number' => '081234567801',
            'department_id' => $this->itDept->id,
        ]);

        $emp = User::where('email', 'ahmad.dani@asiaplastik.com')->first();

        // 2. Manual Edit Employee
        Livewire::test(HcmEmployeeMaster::class)
            ->call('openEditModal', $emp->id)
            ->set('name', 'Ahmad Dani Updated')
            ->set('whatsapp_number', '081234567802')
            ->set('email', 'ahmad.dani@asiaplastik.com')
            ->set('department_id', $this->prodDept->id)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $emp->id,
            'name' => 'Ahmad Dani Updated',
            'whatsapp_number' => '081234567802',
            'department_id' => $this->prodDept->id,
        ]);

        // 3. CSV Bulk Upload
        $csvContent = "name,whatsapp_number,email,department_id\n"
            ."Siti CSV,081234567803,siti.csv@asiaplastik.com,2\n"
            ."Budi CSV,081234567804,budi.csv@asiaplastik.com,1\n";

        $file = UploadedFile::fake()->createWithContent('employees.csv', $csvContent);

        Livewire::test(HcmEmployeeMaster::class)
            ->set('csvFile', $file)
            ->call('importCsv')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Siti CSV',
            'email' => 'siti.csv@asiaplastik.com',
            'department_id' => 2,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Budi CSV',
            'email' => 'budi.csv@asiaplastik.com',
            'department_id' => 1,
        ]);

        // 4. Delete Employee
        Livewire::test(HcmEmployeeMaster::class)
            ->call('confirmDelete', $emp->id)
            ->call('deleteEmployee')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', [
            'id' => $emp->id,
        ]);
    }

    /**
     * MODULE 4: Global Ticket Transparency & RBAC Actions.
     */
    public function test_module_4_global_tickets_transparency_and_rbac_actions(): void
    {
        $itUser = User::create([
            'name' => 'IT Agent',
            'email' => 'it_agent@asiaplastik.com',
            'password' => bcrypt('password123'),
            'department_id' => $this->itDept->id, // Dept 1
            'role' => 'agent',
        ]);

        $hrUser = User::create([
            'name' => 'HR Staff',
            'email' => 'hr_reporter@asiaplastik.com',
            'password' => bcrypt('password123'),
            'department_id' => $this->hrDept->id, // Dept 2
            'role' => 'staff',
        ]);

        // Ticket 1: Target IT Dept
        $itTicket = Ticket::create([
            'sender_id' => $hrUser->id,
            'user_id' => $hrUser->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Server Down di Lantai 2',
            'category' => 'Network',
            'description' => 'Koneksi jaringan terputus',
            'priority' => 'High',
            'status' => 'Pending',
        ]);

        // Ticket 2: Target HR Dept
        $hrTicket = Ticket::create([
            'sender_id' => $itUser->id,
            'user_id' => $itUser->id,
            'target_department_id' => $this->hrDept->id,
            'title' => 'Permohonan Cuti Sakit',
            'category' => 'Payroll',
            'description' => 'Izin cuti 2 hari',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]);

        // 1. As IT User (department_id = 1):
        // IT ticket must show "Handle Ticket" button
        // HR ticket must show "View Only - Not Your Dept" badge
        $this->actingAs($itUser);

        Livewire::test(GlobalTickets::class)
            ->assertSee('Server Down di Lantai 2')
            ->assertSee('Permohonan Cuti Sakit')
            ->assertSee('Handle Ticket')
            ->assertSee('View Only - Not Your Dept');

        // 2. IT User can handle IT ticket
        Livewire::test(GlobalTickets::class)
            ->call('handleTicket', $itTicket->id)
            ->assertHasNoErrors();

        $itTicket->refresh();
        $this->assertEquals('In Progress', $itTicket->status);

        // 3. IT User attempts to handle HR ticket -> Rejected
        Livewire::test(GlobalTickets::class)
            ->call('handleTicket', $hrTicket->id);

        $hrTicket->refresh();
        $this->assertEquals('Pending', $hrTicket->status); // Remains pending, not handled

        // 4. As HR User (department_id = 2):
        // Reversed: HR ticket has "Handle Ticket", IT ticket is "View Only - Not Your Dept"
        $this->actingAs($hrUser);

        Livewire::test(GlobalTickets::class)
            ->assertSee('Server Down di Lantai 2')
            ->assertSee('Permohonan Cuti Sakit')
            ->assertSee('Handle Ticket')
            ->assertSee('View Only - Not Your Dept');
    }
}
