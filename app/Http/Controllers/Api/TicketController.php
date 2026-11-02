<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Format a ticket object for mobile consumption.
     */
    protected function formatTicket(Ticket $ticket): array
    {
        $photoUrl = $ticket->photo_url;
        if ($photoUrl && ! str_starts_with($photoUrl, 'http') && ! str_starts_with($photoUrl, 'data:')) {
            $photoUrl = url($photoUrl);
        }

        return [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'category' => $ticket->category,
            'priority' => $ticket->priority,
            'status' => $ticket->status,
            'description' => $ticket->description,
            'photo_url' => $photoUrl,
            'created_at' => $ticket->created_at?->toIso8601String(),
            'created_at_human' => $ticket->created_at?->diffForHumans(),
            'sender' => $ticket->user ? [
                'id' => $ticket->user->id,
                'name' => $ticket->user->name,
                'avatar' => $ticket->user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($ticket->user->name),
                'department_name' => $ticket->user->department?->name ?? 'Karyawan',
            ] : null,
            'target_department' => $ticket->targetDepartment ? [
                'id' => $ticket->targetDepartment->id,
                'name' => $ticket->targetDepartment->name,
                'icon' => $ticket->targetDepartment->icon,
            ] : null,
            'messages_count' => $ticket->messages()->count(),
        ];
    }

    /**
     * Get tickets submitted by the current authenticated employee.
     */
    public function employeeTickets(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $query = Ticket::with(['targetDepartment', 'user'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('sender_id', $user->id);
            });

        // Filter status
        if ($request->filled('status') && $request->status !== 'All' && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        // Search keyword
        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('category', 'like', $search);
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->get()->map(fn ($t) => $this->formatTicket($t));

        return response()->json([
            'success' => true,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Create a new ticket (Employee).
     */
    public function store(Request $request, WhatsAppNotificationService $waService): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|min:4|max:150',
            'target_department_id' => 'required|exists:departments,id',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'description' => 'required|string|min:5',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:10240',
            'photo_base64' => 'nullable|string',
        ]);

        /** @var User $user */
        $user = $request->user();

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('ticket_attachments', 'public');
        } elseif ($request->filled('photo_base64')) {
            $base64 = $request->photo_base64;
            // Handle raw base64 or data URI
            if (str_starts_with($base64, 'data:image')) {
                $photoPath = $base64;
            } else {
                $photoPath = 'data:image/jpeg;base64,'.$base64;
            }
        }

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'sender_id' => $user->id,
            'target_department_id' => $validated['target_department_id'],
            'title' => $validated['title'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'attachment_path' => $photoPath,
            'status' => 'Pending',
        ]);

        $ticket->load(['targetDepartment', 'user']);

        // Safe async WhatsApp notification
        try {
            $adminPhone = config('services.meta_whatsapp.admin_number');
            if ($adminPhone) {
                $waService->sendHelloWorldTemplate($adminPhone);
            }
        } catch (\Throwable $e) {
            // Silently log and do not break flow
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket kendala Anda telah berhasil diajukan.',
            'ticket' => $this->formatTicket($ticket),
        ], 201);
    }

    /**
     * Get detailed ticket data including full interactive conversation thread.
     */
    public function show(int $id): JsonResponse
    {
        $ticket = Ticket::with(['targetDepartment', 'user.department', 'messages.user.department'])->find($id);

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $messages = $ticket->messages->map(function ($msg) {
            $photoUrl = $msg->photo_url;
            if ($photoUrl && ! str_starts_with($photoUrl, 'http') && ! str_starts_with($photoUrl, 'data:')) {
                $photoUrl = url($photoUrl);
            }

            return [
                'id' => $msg->id,
                'ticket_id' => $msg->ticket_id,
                'message' => $msg->message,
                'photo_url' => $photoUrl,
                'created_at' => $msg->created_at?->toIso8601String(),
                'created_at_human' => $msg->created_at?->diffForHumans(),
                'sender' => $msg->user ? [
                    'id' => $msg->user->id,
                    'name' => $msg->user->name,
                    'role' => $msg->user->role,
                    'avatar' => $msg->user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($msg->user->name),
                    'department_name' => $msg->user->department?->name ?? 'Umum',
                    'is_admin' => $msg->user->role === 'admin' || $msg->user->email === 'user123@gmail.com',
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'ticket' => $this->formatTicket($ticket),
            'messages' => $messages,
        ]);
    }

    /**
     * Send a new message / reply into the ticket conversation thread.
     */
    public function addMessage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|min:1',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:10240',
            'photo_base64' => 'nullable|string',
        ]);

        $ticket = Ticket::find($id);
        if (! $ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        /** @var User $user */
        $user = $request->user();

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('ticket_attachments', 'public');
        } elseif ($request->filled('photo_base64')) {
            $base64 = $request->photo_base64;
            if (str_starts_with($base64, 'data:image')) {
                $photoPath = $base64;
            } else {
                $photoPath = 'data:image/jpeg;base64,'.$base64;
            }
        }

        $message = Message::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'photo_path' => $photoPath,
        ]);

        // If ticket was pending and recipient / support replied, change to In Progress
        if ($ticket->status === 'Pending' && $user->id !== $ticket->user_id) {
            $ticket->update(['status' => 'In Progress']);
        }

        $message->load(['user.department']);

        $photoUrl = $message->photo_url;
        if ($photoUrl && ! str_starts_with($photoUrl, 'http') && ! str_starts_with($photoUrl, 'data:')) {
            $photoUrl = url($photoUrl);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'ticket_id' => $message->ticket_id,
                'message' => $message->message,
                'photo_url' => $photoUrl,
                'created_at' => $message->created_at?->toIso8601String(),
                'created_at_human' => $message->created_at?->diffForHumans(),
                'sender' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'avatar' => $user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($user->name),
                    'department_name' => $user->department?->name ?? 'Umum',
                    'is_admin' => $user->role === 'admin' || $user->email === 'user123@gmail.com',
                ],
            ],
            'updated_ticket_status' => $ticket->status,
        ], 201);
    }

    /**
     * Get KPI Summary for the current employee.
     */
    public function employeeKpi(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $base = Ticket::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('sender_id', $user->id);
        });

        $total = (clone $base)->count();
        $pending = (clone $base)->where('status', 'Pending')->count();
        $inProgress = (clone $base)->where('status', 'In Progress')->count();
        $resolved = (clone $base)->where('status', 'Resolved')->count();

        return response()->json([
            'success' => true,
            'metrics' => [
                'total_tickets' => $total,
                'pending_tickets' => $pending,
                'in_progress_tickets' => $inProgress,
                'resolved_tickets' => $resolved,
                'active_tickets' => $pending + $inProgress,
            ],
        ]);
    }
}
