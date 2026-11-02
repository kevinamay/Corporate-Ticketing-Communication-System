<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HrdController extends Controller
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
                'email' => $ticket->user->email,
                'whatsapp_number' => $ticket->user->whatsapp_number,
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
     * Get Elevated KPI Metrics for HRD Dashboard.
     */
    public function metrics(): JsonResponse
    {
        $totalEmployees = User::where('role', '!=', 'admin')->where('email', '!=', 'user123@gmail.com')->count();
        $pendingApprovals = User::whereNull('email_verified_at')->count();

        $totalTickets = Ticket::count();
        $pendingTickets = Ticket::where('status', 'Pending')->count();
        $inProgressTickets = Ticket::where('status', 'In Progress')->count();
        $resolvedTickets = Ticket::where('status', 'Resolved')->count();

        // Department breakdown
        $departments = Department::withCount('tickets')->get()->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'tickets_count' => $d->tickets_count,
        ]);

        return response()->json([
            'success' => true,
            'metrics' => [
                'total_employees' => $totalEmployees,
                'pending_approvals' => $pendingApprovals,
                'total_tickets' => $totalTickets,
                'pending_tickets' => $pendingTickets,
                'in_progress_tickets' => $inProgressTickets,
                'resolved_tickets' => $resolvedTickets,
                'resolution_rate' => $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 100.0,
                'departments' => $departments,
            ],
        ]);
    }

    /**
     * Get last 7 days ticket volume trend for Chart.
     */
    public function dailyChart(): JsonResponse
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Ticket::whereDate('created_at', $date)->count();
            $days[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->translatedFormat('D, d M'),
                'short_label' => $date->format('d/m'),
                'count' => $count,
            ];
        }

        return response()->json([
            'success' => true,
            'trend' => $days,
        ]);
    }

    /**
     * Get global tickets for HRD & Admin to handle.
     */
    public function tickets(Request $request): JsonResponse
    {
        $query = Ticket::with(['targetDepartment', 'user.department']);

        // Department filter
        if ($request->filled('department_id')) {
            $query->where('target_department_id', $request->department_id);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'All' && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('category', 'like', $search)
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->get()->map(fn ($t) => $this->formatTicket($t));

        return response()->json([
            'success' => true,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Update ticket status (e.g. In Progress, Resolved).
     */
    public function updateTicketStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Resolved',
            'note' => 'nullable|string',
        ]);

        $ticket = Ticket::find($id);
        if (! $ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        $oldStatus = $ticket->status;
        $ticket->update(['status' => $request->status]);

        // If a resolution note is provided, automatically post it as a message from HRD
        if ($request->filled('note')) {
            Message::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'message' => "📌 **Update Status: {$request->status}**\n\n".$request->note,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Status tiket #{$ticket->id} berhasil diperbarui menjadi {$request->status}.",
            'ticket' => $this->formatTicket($ticket),
        ]);
    }

    /**
     * Get list of employees for HCM Master Data view.
     */
    public function employees(Request $request): JsonResponse
    {
        $query = User::with('department')
            ->where('role', '!=', 'admin')
            ->where('email', '!=', 'user123@gmail.com');

        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('whatsapp_number', 'like', $search)
                    ->orWhere('national_id_ktp', 'like', $search);
            });
        }

        if ($request->filled('approval_status')) {
            if ($request->approval_status === 'pending') {
                $query->whereNull('email_verified_at');
            } elseif ($request->approval_status === 'approved') {
                $query->whereNotNull('email_verified_at');
            }
        }

        $employees = $query->orderBy('id', 'desc')->get()->map(function ($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->name,
                'email' => $emp->email,
                'whatsapp_number' => $emp->whatsapp_number,
                'national_id_ktp' => $emp->national_id_ktp,
                'gender' => $emp->gender,
                'complete_address' => $emp->complete_address,
                'department_id' => $emp->department_id,
                'department_name' => $emp->department?->name ?? 'Belum Ditentukan',
                'is_approved' => $emp->email_verified_at !== null,
                'approved_at' => $emp->email_verified_at?->toIso8601String(),
                'avatar' => $emp->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($emp->name).'&background=0284c7&color=fff',
                'created_at' => $emp->created_at?->toIso8601String(),
                'created_at_human' => $emp->created_at?->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'employees' => $employees,
        ]);
    }

    /**
     * Toggle approval status (ACC) for an employee.
     */
    public function toggleEmployeeApproval(int $id): JsonResponse
    {
        $employee = User::find($id);

        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.',
            ], 404);
        }

        if ($employee->email === 'user123@gmail.com') {
            return response()->json([
                'success' => false,
                'message' => 'Status Administrator IT utama tidak dapat diubah.',
            ], 403);
        }

        if ($employee->email_verified_at !== null) {
            $employee->update(['email_verified_at' => null]);
            $isApproved = false;
            $message = "Persetujuan akun {$employee->name} telah dinonaktifkan (Menunggu ACC).";
        } else {
            $employee->update(['email_verified_at' => now()]);
            $isApproved = true;
            $message = "Akun {$employee->name} telah berhasil di-ACC dan diaktifkan.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'employee_id' => $employee->id,
            'is_approved' => $isApproved,
        ]);
    }
}
