<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Store a newly created ticket in the database and notify IT Admin via WhatsApp.
     */
    public function store(Request $request, WhatsAppNotificationService $whatsAppService): RedirectResponse|JsonResponse
    {
        // 1. Validate incoming form/API data
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:150',
            'category' => 'required|string|max:50',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'target_department_id' => 'required|exists:departments,id',
            'description' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        // 2. Persist the ticket to the database inside a transaction
        $ticket = DB::transaction(function () use ($validated, $request) {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('ticket_attachments', 'public');
            }

            $currentUserId = Auth::id() ?? 1;

            return Ticket::create([
                'user_id' => $currentUserId,
                'sender_id' => $currentUserId,
                'target_department_id' => $validated['target_department_id'],
                'title' => $validated['title'],
                'category' => $validated['category'],
                'priority' => $validated['priority'],
                'description' => $validated['description'],
                'photo_path' => $attachmentPath,
                'attachment_path' => $attachmentPath,
                'status' => 'Pending',
            ]);
        });

        // 3. Send WhatsApp Notification to IT Admin immediately
        // CRITICAL: Isolated in a try-catch block so 3rd-party API errors NEVER crash the user's flow
        try {
            $adminNumber = config('services.meta_whatsapp.admin_number');

            // Send using pre-approved 'hello_world' template (required for Meta test phone numbers)
            $isNotified = $whatsAppService->sendHelloWorldTemplate($adminNumber);

            if ($isNotified) {
                Log::info("WhatsApp notification sent to IT Admin for Ticket #{$ticket->id}");
            } else {
                Log::warning("WhatsApp notification was not delivered for Ticket #{$ticket->id}. Check Meta API logs.");
            }
        } catch (\Throwable $e) {
            // Log the error for monitoring (e.g. Sentry/Datadog) without interrupting response
            Log::error("WhatsApp Notification failed for Ticket #{$ticket->id}: ".$e->getMessage(), [
                'exception' => $e,
            ]);
        }

        // 4. Return appropriate response (API or Blade web redirect)
        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tiket berhasil dibuat dan notifikasi WhatsApp sedang diteruskan ke Admin IT.',
                'ticket' => $ticket,
            ], 201);
        }

        return redirect()->route('dashboard')->with('success', 'Tiket berhasil dibuat! Admin IT telah menerima notifikasi WhatsApp.');
    }
}
