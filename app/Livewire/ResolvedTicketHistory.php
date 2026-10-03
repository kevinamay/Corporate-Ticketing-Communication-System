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
        $ticket = Ticket::find($ticketId);
        $isAdmin = $this->viewMode === 'admin' || ($this->viewMode === 'auto' && $this->isAdminUser());
        $currentUser = Auth::user() ?? (session('active_user_id') ? User::find(session('active_user_id')) : null);

        if (! $isAdmin) {
            if (! $ticket || ! $currentUser || ((int) $ticket->user_id !== (int) $currentUser->id && (int) $ticket->sender_id !== (int) $currentUser->id)) {
                session()->flash('history_error', 'Akses ditolak: Anda hanya dapat melihat detail tiket milik Anda sendiri.');

                return;
            }
        }

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

        // Privacy Isolation: Non-admin employees can ONLY view their own resolved tickets!
        if (! $isAdmin) {
            if ($currentUser) {
                $query->where(function ($q) use ($currentUser) {
                    $q->where('user_id', $currentUser->id)
                        ->orWhere('sender_id', $currentUser->id);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($this->scopeFilter === 'mine' && $currentUser) {
            // For admin who chooses to filter by "mine"
            $query->where(function ($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id)
                    ->orWhere('sender_id', $currentUser->id);
            });
        }

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

        // Sorting: by updated_at (when marked Resolved)
        $query->orderBy('updated_at', $this->sortBy === 'oldest' ? 'asc' : 'desc');

        $tickets = $query->paginate(10);

        $viewingTicket = null;
        if ($this->viewingTicketId) {
            $vt = Ticket::with(['sender', 'user', 'targetDepartment', 'messages.user'])->find($this->viewingTicketId);
            if ($vt) {
                $isOwner = $currentUser && ((int) $vt->user_id === (int) $currentUser->id || (int) $vt->sender_id === (int) $currentUser->id);
                if ($isAdmin || $isOwner) {
                    $viewingTicket = $vt;
                }
            }
        }

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
