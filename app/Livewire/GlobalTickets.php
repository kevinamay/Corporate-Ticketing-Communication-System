<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GlobalTickets extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $departmentFilter = 'all';

    public string $priorityFilter = 'all';

    public ?int $viewingTicketId = null;

    public bool $isDetailModalOpen = false;

    public string $replyMessage = '';

    public $replyPhoto = null;

    public string $ticketStatusToUpdate = '';

    /**
     * Check if a user has authority to process, handle, or reply to a ticket.
     * Admin and Siti (HRD) have global authority across all departments.
     * Department agents have authority for tickets directed to their department.
     */
    public function isAuthorizedForTicket(?\App\Models\User $user, ?Ticket $ticket): bool
    {
        if (! $user || ! $ticket) {
            return false;
        }

        // Global admin & HR management authority
        if (
            $user->role === 'admin' ||
            $user->email === 'siti.hrd@asiaplastik.com'
        ) {
            return true;
        }

        // Target department staff/agent authority
        return (int) $user->department_id === (int) $ticket->target_department_id;
    }

    /**
     * Handle/process a ticket if authorized for the current user's department or admin.
     */
    public function handleTicket(int $ticketId): void
    {
        $user = Auth::user() ?? (session('active_user_id') ? \App\Models\User::find(session('active_user_id')) : null);

        if (! $user) {
            session()->flash('unauthorized_error', 'Silakan login terlebih dahulu untuk memproses tiket.');

            return;
        }

        $ticket = Ticket::with(['targetDepartment', 'sender'])->find($ticketId);

        if (! $ticket) {
            return;
        }

        // Authorization Check
        if (! $this->isAuthorizedForTicket($user, $ticket)) {
            session()->flash('unauthorized_error', 'Akses ditolak: Anda hanya dapat memproses tiket yang ditujukan untuk departemen Anda.');

            return;
        }

        // If status is Pending or Open, update to In Progress
        if (in_array($ticket->status, ['Pending', 'Open'], true)) {
            $ticket->update(['status' => 'In Progress']);
            $this->dispatch('ticketUpdated', ticketId: $ticket->id);
        }

        $this->viewingTicketId = $ticket->id;
        $this->ticketStatusToUpdate = $ticket->status;
        $this->isDetailModalOpen = true;

        session()->flash('handle_success', "Tiket #{$ticket->id} berhasil diambil untuk penanganan. Anda dapat menulis jawaban atau solusi di bawah.");
    }

    /**
     * Send reply / answer to the ticket.
     */
    public function sendTicketReply(int $ticketId): void
    {
        $user = Auth::user() ?? (session('active_user_id') ? \App\Models\User::find(session('active_user_id')) : null);

        if (! $user) {
            session()->flash('unauthorized_error', 'Silakan login terlebih dahulu untuk menjawab tiket.');

            return;
        }

        $ticket = Ticket::find($ticketId);

        if (! $ticket) {
            return;
        }

        if (! $this->isAuthorizedForTicket($user, $ticket) && $ticket->sender_id !== $user->id) {
            session()->flash('unauthorized_error', 'Akses ditolak untuk menjawab tiket ini.');

            return;
        }

        $this->validate([
            'replyMessage' => 'required|string|min:2|max:2000',
            'replyPhoto' => 'nullable|image|max:10240',
        ], [
            'replyMessage.required' => 'Pesan jawaban / tanggapan wajib diisi.',
            'replyMessage.min' => 'Pesan jawaban minimal 2 karakter.',
            'replyPhoto.image' => 'File bukti harus berupa gambar (JPG, PNG, WebP).',
            'replyPhoto.max' => 'Ukuran file gambar maksimal 10MB.',
        ]);

        $photoPath = null;
        if ($this->replyPhoto) {
            try {
                $realPath = $this->replyPhoto->getRealPath();
                $mime = $this->replyPhoto->getMimeType() ?: 'image/jpeg';
                if ($realPath && file_exists($realPath)) {
                    $photoPath = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($realPath));
                } else {
                    $storedPath = $this->replyPhoto->store('reply_attachments', 'public');
                    $photoPath = '/storage/'.$storedPath;
                }
            } catch (\Throwable $e) {
                Log::warning('Reply photo store fallback: '.$e->getMessage());
                try {
                    $photoPath = 'data:'.$this->replyPhoto->getMimeType().';base64,'.base64_encode(file_get_contents($this->replyPhoto->getRealPath()));
                } catch (\Throwable $ex) {
                    $photoPath = null;
                }
            }
        }

        $messageData = [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => trim($this->replyMessage),
        ];

        // Ensure schema compatibility with serverless SQLite
        try {
            if (Schema::hasColumn('messages', 'photo_path')) {
                $messageData['photo_path'] = $photoPath;
            } else {
                try {
                    DB::statement('ALTER TABLE messages ADD COLUMN photo_path TEXT NULL');
                    $messageData['photo_path'] = $photoPath;
                } catch (\Throwable $ex) {
                    // Ignore and save without photo_path to prevent 500 error
                }
            }
        } catch (\Throwable $e) {
            $messageData['photo_path'] = $photoPath;
        }

        // Create message response
        \App\Models\Message::create($messageData);

        // Update status if selected
        if (! empty($this->ticketStatusToUpdate) && in_array($this->ticketStatusToUpdate, ['Pending', 'Open', 'In Progress', 'Resolved'], true)) {
            $ticket->update(['status' => $this->ticketStatusToUpdate]);
        }

        $this->replyMessage = '';
        $this->replyPhoto = null;
        $this->dispatch('ticketUpdated', ticketId: $ticket->id);
        session()->flash('reply_success', 'Tanggapan / jawaban berhasil dikirim ke pelapor!');
    }

    public function removeReplyPhoto(): void
    {
        $this->replyPhoto = null;
    }

    /**
     * Quick status update for ticket.
     */
    public function updateTicketStatus(int $ticketId, string $status): void
    {
        $user = Auth::user() ?? (session('active_user_id') ? \App\Models\User::find(session('active_user_id')) : null);

        if (! $user) {
            session()->flash('unauthorized_error', 'Silakan login terlebih dahulu untuk mengubah status tiket.');

            return;
        }

        $ticket = Ticket::find($ticketId);

        if (! $ticket || ! $this->isAuthorizedForTicket($user, $ticket)) {
            session()->flash('unauthorized_error', 'Akses ditolak: Anda tidak memiliki wewenang untuk memperbarui status tiket ini.');

            return;
        }

        if (in_array($status, ['Pending', 'Open', 'In Progress', 'Resolved'], true)) {
            $oldStatus = $ticket->status;
            $ticket->update(['status' => $status]);
            $this->ticketStatusToUpdate = $status;
            $this->dispatch('ticketUpdated', ticketId: $ticket->id);
            session()->flash('handle_success', "Status tiket #{$ticket->id} berhasil diperbarui dari {$oldStatus} menjadi {$status}.");
            session()->flash('reply_success', "Status tiket #{$ticket->id} berhasil diperbarui dari {$oldStatus} menjadi {$status}.");
        }
    }

    /**
     * Open Ticket Detail Modal.
     */
    public function viewTicketDetail(int $ticketId): void
    {
        $this->viewingTicketId = $ticketId;
        $ticket = Ticket::find($ticketId);
        $this->ticketStatusToUpdate = $ticket?->status ?? 'In Progress';
        $this->replyMessage = '';
        $this->replyPhoto = null;
        $this->isDetailModalOpen = true;
    }

    /**
     * Close Ticket Detail Modal.
     */
    public function closeDetailModal(): void
    {
        $this->isDetailModalOpen = false;
        $this->viewingTicketId = null;
        $this->replyMessage = '';
        $this->replyPhoto = null;
        $this->ticketStatusToUpdate = '';
    }

    public function render(): View
    {
        $query = Ticket::query()
            ->with(['sender', 'user', 'targetDepartment', 'messages']);

        // Search Filter
        if (trim($this->search) !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('category', 'like', $searchTerm)
                    ->orWhere('id', 'like', str_replace('#', '', $this->search))
                    ->orWhereHas('sender', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('targetDepartment', function ($dq) use ($searchTerm) {
                        $dq->where('name', 'like', $searchTerm);
                    });
            });
        }

        // Status Filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Department Filter
        if ($this->departmentFilter !== 'all') {
            $query->where('target_department_id', (int) $this->departmentFilter);
        }

        // Priority Filter
        if ($this->priorityFilter !== 'all') {
            $query->where('priority', $this->priorityFilter);
        }

        $tickets = $query->latest()->paginate(10);

        $viewingTicket = $this->viewingTicketId
            ? Ticket::with(['sender', 'targetDepartment', 'messages.user'])->find($this->viewingTicketId)
            : null;

        return view('livewire.global-tickets', [
            'tickets' => $tickets,
            'departments' => Department::all(),
            'viewingTicket' => $viewingTicket,
            'currentUser' => Auth::user() ?? (session('active_user_id') ? \App\Models\User::find(session('active_user_id')) : null),
        ]);
    }
}
