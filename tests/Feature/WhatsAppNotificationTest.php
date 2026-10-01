<?php

namespace Tests\Feature;

use App\Livewire\TicketForm;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Department $itDept;

    protected Department $hrDept;

    protected User $sender;

    protected Ticket $ticket;

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
            'name' => 'Human Resources',
            'icon' => 'users',
            'description' => 'HR Division',
        ]);

        $this->sender = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@asiaplastik.com',
            'password' => bcrypt('secret123'),
            'department_id' => $this->hrDept->id,
            'role' => 'staff',
        ]);

        $this->ticket = Ticket::create([
            'user_id' => $this->sender->id,
            'sender_id' => $this->sender->id,
            'target_department_id' => $this->itDept->id,
            'title' => 'Komputer Mati Mendadak',
            'category' => 'Hardware',
            'description' => 'CPU di bagian HR mati saat proses payroll.',
            'priority' => 'High',
            'status' => 'Pending',
        ]);
    }

    public function test_message_template_matches_exact_format_and_wib_timezone(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-29 14:30:00', 'UTC')); // 21:30 WIB
        $this->ticket->created_at = Carbon::now();
        $this->ticket->save();

        $service = new WhatsAppNotificationService;
        $message = $service->buildNewTicketMessage($this->ticket);

        $expectedUrl = route('dashboard', ['ticket' => $this->ticket->id], true);

        $this->assertStringContainsString('*TIKET BARU MASUK*', $message);
        $this->assertStringContainsString('*Pengirim:* Budi Santoso', $message);
        $this->assertStringContainsString('*Divisi:* Human Resources', $message);
        $this->assertStringContainsString('*Masalah:* Komputer Mati Mendadak', $message);
        $this->assertStringContainsString('*Waktu:*', $message);
        $this->assertStringContainsString('WIB', $message);
        $this->assertStringContainsString('Segera proses tiket ini dengan klik link berikut:', $message);
        $this->assertStringContainsString($expectedUrl, $message);
    }

    public function test_notification_sent_successfully_via_meta_cloud_api(): void
    {
        Config::set('services.meta_whatsapp.phone_number_id', '123456789012345');
        Config::set('services.meta_whatsapp.access_token', 'EAAB_test_token');
        Config::set('services.meta_whatsapp.admin_number', '081234567890');
        Config::set('services.meta_whatsapp.api_version', 'v20.0');

        $expectedEndpoint = 'https://graph.facebook.com/v20.0/123456789012345/messages';

        Http::fake([
            $expectedEndpoint => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [['input' => '6281234567890', 'wa_id' => '6281234567890']],
                'messages' => [['id' => 'wamid.HBgL...']],
            ], 200),
        ]);

        $service = new WhatsAppNotificationService;
        $result = $service->sendNewTicketNotification($this->ticket);

        $this->assertTrue($result);

        Http::assertSent(function ($request) use ($expectedEndpoint) {
            return $request->url() === $expectedEndpoint
                && $request->hasHeader('Authorization', 'Bearer EAAB_test_token')
                && $request['messaging_product'] === 'whatsapp'
                && $request['recipient_type'] === 'individual'
                && $request['to'] === '6281234567890'
                && $request['type'] === 'text'
                && str_contains($request['text']['body'], '*TIKET BARU MASUK*')
                && str_contains($request['text']['body'], 'Komputer Mati Mendadak');
        });
    }

    public function test_notification_handles_meta_api_failure_silently_without_throwing(): void
    {
        Config::set('services.meta_whatsapp.phone_number_id', '123456789012345');
        Config::set('services.meta_whatsapp.access_token', 'EAAB_test_token');
        Config::set('services.meta_whatsapp.admin_number', '081234567890');

        // Simulate Meta API 400 error (e.g., token expired or number not registered)
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'error' => [
                    'message' => 'Invalid OAuth access token.',
                    'type' => 'OAuthException',
                    'code' => 190,
                ],
            ], 400),
        ]);

        $service = new WhatsAppNotificationService;
        $result = $service->sendNewTicketNotification($this->ticket);

        $this->assertFalse($result); // Fails gracefully, no exception thrown
    }

    public function test_notification_skipped_if_credentials_are_empty(): void
    {
        Config::set('services.meta_whatsapp.phone_number_id', null);
        Config::set('services.meta_whatsapp.access_token', null);
        Config::set('services.meta_whatsapp.admin_number', null);
        Config::set('services.waha.base_url', null);
        Config::set('services.waha.admin_number', null);

        Http::fake();

        $service = new WhatsAppNotificationService;
        $result = $service->sendNewTicketNotification($this->ticket);

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    public function test_ticket_form_submits_and_triggers_meta_whatsapp_notification(): void
    {
        Config::set('services.meta_whatsapp.phone_number_id', '123456789012345');
        Config::set('services.meta_whatsapp.access_token', 'EAAB_test_token');
        Config::set('services.meta_whatsapp.admin_number', '089876543210');
        Config::set('services.meta_whatsapp.api_version', 'v20.0');

        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.12345']],
            ], 200),
        ]);

        $this->actingAs($this->sender);

        Livewire::test(TicketForm::class)
            ->set('title', 'Koneksi Printer Rusak')
            ->set('category', 'Hardware')
            ->set('priority', 'High')
            ->set('description', 'Printer kasir macet dan tidak dapat mencetak struk.')
            ->set('target_department_id', $this->itDept->id)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSuccess', true)
            ->assertDispatched('ticketCreated');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Koneksi Printer Rusak',
            'sender_id' => $this->sender->id,
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '123456789012345/messages')
                && $request['to'] === '6289876543210'
                && str_contains($request['text']['body'], 'Koneksi Printer Rusak');
        });
    }

    public function test_hello_world_template_sent_successfully_via_meta_cloud_api(): void
    {
        Config::set('services.meta_whatsapp.phone_number_id', '1389469127579066');
        Config::set('services.meta_whatsapp.access_token', 'EAAB_test_token');
        Config::set('services.meta_whatsapp.admin_number', '6285784694910');
        Config::set('services.meta_whatsapp.api_version', 'v20.0');

        $expectedEndpoint = 'https://graph.facebook.com/v20.0/1389469127579066/messages';

        Http::fake([
            $expectedEndpoint => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [['input' => '6285784694910', 'wa_id' => '6285784694910']],
                'messages' => [['id' => 'wamid.HBgL123456789']],
            ], 200),
        ]);

        $service = new WhatsAppNotificationService;
        $result = $service->sendHelloWorldTemplate();

        $this->assertTrue($result);

        Http::assertSent(function ($request) use ($expectedEndpoint) {
            return $request->url() === $expectedEndpoint
                && $request->hasHeader('Authorization', 'Bearer EAAB_test_token')
                && $request['messaging_product'] === 'whatsapp'
                && $request['recipient_type'] === 'individual'
                && $request['to'] === '6285784694910'
                && $request['type'] === 'template'
                && $request['template']['name'] === 'hello_world'
                && $request['template']['language']['code'] === 'en_US';
        });
    }

    public function test_notification_sent_successfully_via_waha_gateway(): void
    {
        Config::set('services.meta_whatsapp.phone_number_id', null);
        Config::set('services.meta_whatsapp.access_token', null);
        Config::set('services.waha.base_url', 'http://localhost:3000');
        Config::set('services.waha.api_key', 'e8928adf08ec4cfd8b30dea033ee38bc');
        Config::set('services.waha.session', 'default');
        Config::set('services.waha.admin_number', '6285784694910');

        Http::fake([
            'http://localhost:3000/api/sendText' => Http::response([
                'id' => 'true_6285784694910@c.us_3EB012345678',
                'timestamp' => 1727780000,
            ], 200),
        ]);

        $service = new WhatsAppNotificationService;
        $result = $service->sendNewTicketNotification($this->ticket);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://localhost:3000/api/sendText'
                && $request->hasHeader('X-Api-Key', 'e8928adf08ec4cfd8b30dea033ee38bc')
                && $request['chatId'] === '6285784694910@c.us'
                && str_contains($request['text'], '*TIKET BARU MASUK*')
                && str_contains($request['text'], 'Komputer Mati Mendadak');
        });
    }
}
