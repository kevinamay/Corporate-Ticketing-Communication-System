<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
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

        $this->assertStringContainsString('🚨 *TIKET BARU MASUK!* 🚨', $message);
        $this->assertStringContainsString('👤 *Pengirim:* Budi Santoso', $message);
        $this->assertStringContainsString('🏢 *Divisi:* Human Resources', $message);
        $this->assertStringContainsString('📝 *Masalah:* Komputer Mati Mendadak', $message);
        $this->assertStringContainsString('🕒 *Waktu:*', $message);
        $this->assertStringContainsString('WIB', $message);
        $this->assertStringContainsString('Segera proses tiket ini dengan klik link berikut:', $message);
        $this->assertStringContainsString('👉 '.$expectedUrl, $message);
    }

    public function test_notification_sent_successfully_via_http_post(): void
    {
        Config::set('services.whatsapp.token', 'test_wa_token_123');
        Config::set('services.whatsapp.admin_number', '081234567890');
        Config::set('services.whatsapp.url', 'https://api.fonnte.com/send');

        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'target' => ['081234567890'],
            ], 200),
        ]);

        $service = new WhatsAppNotificationService;
        $result = $service->sendNewTicketNotification($this->ticket);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request->hasHeader('Authorization', 'test_wa_token_123')
                && $request['target'] === '081234567890'
                && str_contains($request['message'], '🚨 *TIKET BARU MASUK!* 🚨');
        });
    }

    public function test_notification_handles_gateway_failure_silently_without_throwing(): void
    {
        Config::set('services.whatsapp.token', 'test_wa_token_123');
        Config::set('services.whatsapp.admin_number', '081234567890');
        Config::set('services.whatsapp.url', 'https://api.fonnte.com/send');

        // Simulate Gateway 500 error / timeout
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => false, 'reason' => 'device disconnected'], 500),
        ]);

        $service = new WhatsAppNotificationService;
        $result = $service->sendNewTicketNotification($this->ticket);

        $this->assertFalse($result); // Fails gracefully, no exception thrown
    }

    public function test_notification_skipped_if_credentials_are_empty(): void
    {
        Config::set('services.whatsapp.token', null);
        Config::set('services.whatsapp.admin_number', null);

        Http::fake();

        $service = new WhatsAppNotificationService;
        $result = $service->sendNewTicketNotification($this->ticket);

        $this->assertFalse($result);
        Http::assertNothingSent();
    }
}
