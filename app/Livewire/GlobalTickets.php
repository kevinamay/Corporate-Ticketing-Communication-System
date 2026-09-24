<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalTickets extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $departmentFilter = 'all';

    public string $priorityFilter = 'all';

    public ?int $viewingTicketId = null;

    public bool $isDetailModalOpen = false;

    /**
     * Reset pagination when search or filters change.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    #[On('ticketCreated')]
    public function onTicketCreated(int $ticketId): void
    {
        // Re-renders view automatically
    }

    #[On('ticketUpdated')]
    public function onTicketUpdated(int $ticketId): void
    {
        // Re-renders view automatically
    }

    #[On('ticketDeleted')]
    public function onTicketDeleted(int $ticketId): void
    {
        if ($this->viewingTicketId === $ticketId) {
            $this->isDetailModalOpen = false;
            $this->viewingTicketId = null;
        }
    }

    /**
     * Handle/process a ticket if authorized for the current user's department.
     */
    public function handleTicket(int $ticketId): void
    {
        $user = Auth::user();

        if (! $user) {
            session()->flash('unauthorized_error', 'Silakan login terlebih dahulu untuk memproses tiket.');

            return;
        }

        $ticket = Ticket::with(['targetDepartment', 'sender'])->find($ticketId);

        if (! $ticket) {
            return;
        }

        // Strict Authorization Check: User's department must match target department
        if ((int) $user->department_id !== (int) $ticket->target_department_id) {
            session()->flash('unauthorized_error', 'Akses ditolak: Anda hanya dapat memproses tiket yang ditujukan untuk departemen Anda.');

            return;
        }

        // If status is Pending or Open, update to In Progress
        if (in_array($ticket->status, ['Pending', 'Open'], true)) {
            $ticket->update(['status' => 'In Progress']);
            $this->dispatch('ticketUpdated', ticketId: $ticket->id);
        }

        // Dispatch selection to chat pane
        $this->dispatch('ticketSelected', ticketId: $ticket->id);

        session()->flash('handle_success', "Tiket #{$ticket->id} berhasil diambil dan kini sedang ditangani oleh divisi Anda.");

        if ($this->isDetailModalOpen) {
            $this->isDetailModalOpen = false;
        }
    }

    /**
     * Open Ticket Detail Modal.
     */
    public function viewTicketDetail(int $ticketId): void
    {
        $this->viewingTicketId = $ticketId;
        $this->isDetailModalOpen = true;
    }

    /**
     * Close Ticket Detail Modal.
     */
    public function closeDetailModal(): void
    {
        $this->isDetailModalOpen = false;
        $this->viewingTicketId = null;
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
            'currentUser' => Auth::user(),
        ]);
    }
}
