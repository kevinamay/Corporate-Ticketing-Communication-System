<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ResolvedTicketHistory extends Component
{
    use WithPagination;

    public string $viewMode = 'auto'; // 'admin', 'employee', or 'auto'

    public string $search = '';

    public string $departmentFilter = 'all';

    public string $scopeFilter = 'all'; // 'all' (Semua Tiket Selesai) or 'mine' (Tiket Saya yang Selesai)

    public string $sortBy = 'latest'; // 'latest' or 'oldest'

    public ?int $viewingTicketId = null;

    public bool $isDetailModalOpen = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function updatingScopeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    /**
     * Realtime listeners: auto-refresh when any ticket is created, updated, or status changed.
     */
    #[On('ticketCreated')]
    #[On('ticketStatusUpdated')]
    #[On('ticketDeleted')]
    #[On('ticketUpdated')]
    public function refreshHistory(): void
    {
        // Re-renders component automatically
    }

    public function viewDetail(int $ticketId): void
    {
        $this->viewingTicketId = $ticketId;
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal(): void
    {
        $this->isDetailModalOpen = false;
        $this->viewingTicketId = null;
    }

    public function isAdminUser(): bool
    {
        $user = Auth::user() ?? (session('active_user_id') ? User::find(session('active_user_id')) : null);

        return $user && ($user->role === 'admin' || $user->email === 'user123@gmail.com');
    }

    public function render(): View
    {
        $currentUser = Auth::user() ?? (session('active_user_id') ? User::find(session('active_user_id')) : null);
        $isAdmin = $this->viewMode === 'admin' || ($this->viewMode === 'auto' && $this->isAdminUser());

        $query = Ticket::query()
            ->where('status', 'Resolved')
            ->with(['sender', 'user', 'targetDepartment', 'messages.user']);

        // Search Filter
        if (trim($this->search) !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $cleanId = str_replace('#', '', trim($this->search));

            $query->where(function ($q) use ($searchTerm, $cleanId) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('category', 'like', $searchTerm);

                if (is_numeric($cleanId)) {
                    $q->orWhere('id', (int) $cleanId);
                }

                $q->orWhereHas('sender', function ($sq) use ($searchTerm) {
                    $sq->where('name', 'like', $searchTerm);
                })->orWhereHas('targetDepartment', function ($dq) use ($searchTerm) {
                    $dq->where('name', 'like', $searchTerm);
                })->orWhereHas('messages', function ($mq) use ($searchTerm) {
                    $mq->where('message', 'like', $searchTerm);
                });
            });
        }

        // Department Filter
        if ($this->departmentFilter !== 'all') {
            $query->where('target_department_id', (int) $this->departmentFilter);
        }

        // Scope Filter (specifically for employee: all completed or mine)
        if ($this->scopeFilter === 'mine' && $currentUser) {
            $query->where(function ($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id)
                    ->orWhere('sender_id', $currentUser->id);
            });
        }

        // Sorting: by updated_at (when marked Resolved)
        $query->orderBy('updated_at', $this->sortBy === 'oldest' ? 'asc' : 'desc');

        $tickets = $query->paginate(10);

        $viewingTicket = $this->viewingTicketId
            ? Ticket::with(['sender', 'user', 'targetDepartment', 'messages.user'])->find($this->viewingTicketId)
            : null;

        $totalResolvedAll = Ticket::where('status', 'Resolved')->count();
        $totalResolvedMine = $currentUser
            ? Ticket::where('status', 'Resolved')
                ->where(function ($q) use ($currentUser) {
                    $q->where('user_id', $currentUser->id)
                        ->orWhere('sender_id', $currentUser->id);
                })->count()
            : 0;

        return view('livewire.resolved-ticket-history', [
            'tickets' => $tickets,
            'departments' => Department::orderBy('name')->get(),
            'viewingTicket' => $viewingTicket,
            'currentUser' => $currentUser,
            'isAdmin' => $isAdmin,
            'totalResolvedAll' => $totalResolvedAll,
            'totalResolvedMine' => $totalResolvedMine,
        ]);
    }
}
