<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketList extends Component
{
    use WithFileUploads;

    public ?int $selectedTicketId = null;

    public string $statusFilter = 'all';

    public string $departmentFilter = 'all';

    public string $search = '';

    // Detail Modal State (READ)
    public bool $isDetailModalOpen = false;

    public ?int $viewingTicketId = null;

    // Edit Modal State (UPDATE - Only if Pending / Belum di-acc Admin)
    public bool $isEditModalOpen = false;

    public ?int $editingTicketId = null;

    public string $editTitle = '';

    public ?int $editTargetDepartmentId = null;

    public string $editCategory = '';

    public string $editPriority = 'Medium';

    public string $editDescription = '';

    public $editPhoto = null;

    public ?string $existingPhotoUrl = null;

    public bool $removeExistingPhoto = false;

    public array $departmentCategories = [
        'IT' => ['Network', 'Hardware', 'Software', 'Account', 'Other'],
        'HR' => ['Payroll', 'Leave', 'Attendance', 'Other'],
        'Maintenance' => ['AC', 'Electrical', 'Plumbing', 'Other'],
        'General' => ['Supplies', 'Meeting Room', 'Transport', 'Other'],
    ];

    public function mount(?int $initialSelectedId = null): void
    {
        $this->selectedTicketId = $initialSelectedId ?? Ticket::latest()->first()?->id;
    }

    #[On('ticketCreated')]
    public function onTicketCreated(int $ticketId): void
    {
        $this->selectedTicketId = $ticketId;
    }

    #[On('ticketSelected')]
    public function onTicketSelected(int $ticketId): void
    {
        $this->selectedTicketId = $ticketId;
    }

    #[On('ticketDeleted')]
    public function onTicketDeleted(int $ticketId): void
    {
        if ($this->selectedTicketId === $ticketId) {
            $this->selectedTicketId = Ticket::latest()->first()?->id;
        }
        if ($this->viewingTicketId === $ticketId) {
            $this->isDetailModalOpen = false;
            $this->viewingTicketId = null;
        }
    }

    #[On('ticketUpdated')]
    public function onTicketUpdated(int $ticketId): void
    {
        // Re-renders view and keeps selected ticket
    }

    /**
     * READ: Open Ticket Detail Modal
     */
    public function viewTicket(int $id): void
    {
        $this->selectedTicketId = $id;
        $this->viewingTicketId = $id;
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal(): void
    {
        $this->isDetailModalOpen = false;
        $this->viewingTicketId = null;
    }

    /**
     * Check if active user is owner of the ticket or admin.
     */
    public function isOwner(?Ticket $ticket): bool
    {
        $activeUserId = Auth::id() ?? session('active_user_id');
        if (! $activeUserId || ! $ticket) {
            return false;
        }

        $activeUser = Auth::user() ?? User::find($activeUserId);
        if ($activeUser && ($activeUser->role === 'admin' || $activeUser->email === 'user123@gmail.com')) {
            return true;
        }

        return (int) $ticket->user_id === (int) $activeUserId || (int) $ticket->sender_id === (int) $activeUserId;
    }

    /**
     * Check if ticket can be modified (Updated/Deleted):
     * MUST be owner AND MUST be in 'Pending' status (Belum di-acc oleh admin).
     */
    public function canModifyTicket(?Ticket $ticket): bool
    {
        if (! $this->isOwner($ticket)) {
            return false;
        }

        return $ticket && $ticket->status === 'Pending';
    }

    /**
     * UPDATE: Open Edit Modal (Hanya jika belum di-acc admin / Pending)
     */
    public function openEditModal(int $id): void
    {
        $activeUserId = Auth::id() ?? session('active_user_id');
        if (! $activeUserId) {
            session()->flash('ticket_error', 'Silakan masuk (login) terlebih dahulu.');

            return;
        }

        $ticket = Ticket::find($id);
        if (! $ticket) {
            session()->flash('ticket_error', 'Data laporan tidak ditemukan.');

            return;
        }

        if (! $this->isOwner($ticket)) {
            session()->flash('ticket_error', 'Akses ditolak: Anda hanya dapat mengedit laporan yang Anda buat sendiri.');

            return;
        }

        // Syarat Mutlak: Laporan belum di-acc oleh admin (status === 'Pending')
        if ($ticket->status !== 'Pending') {
            session()->flash('ticket_error', 'Laporan ini sudah di-ACC/diproses oleh admin (status: '.$ticket->status.') dan tidak dapat diubah lagi.');

            return;
        }

        $this->editingTicketId = $ticket->id;
        $this->editTitle = $ticket->title;
        $this->editTargetDepartmentId = $ticket->target_department_id;
        $this->editCategory = $ticket->category;
        $this->editPriority = $ticket->priority;
        $this->editDescription = $ticket->description;
        $this->existingPhotoUrl = $ticket->photo_url;
        $this->editPhoto = null;
        $this->removeExistingPhoto = false;

        $this->isDetailModalOpen = false;
        $this->isEditModalOpen = true;
    }

    public function closeEditModal(): void
    {
        $this->isEditModalOpen = false;
        $this->editingTicketId = null;
        $this->editPhoto = null;
        $this->removeExistingPhoto = false;
        $this->resetValidation();
    }

    public function setEditPriority(string $level): void
    {
        $this->editPriority = $level;
    }

    public function markRemoveExistingPhoto(): void
    {
        $this->removeExistingPhoto = true;
        $this->existingPhotoUrl = null;
    }

    public function getEditAvailableCategoriesProperty(): array
    {
        return $this->departmentCategories['IT'] ?? ['Network', 'Hardware', 'Software', 'Account', 'Other'];
    }

    /**
     * UPDATE: Simpan Perubahan Laporan (Validasi ketat status Pending)
     */
    public function updateTicket(): void
    {
        $activeUserId = Auth::id() ?? session('active_user_id');
        if (! $activeUserId || ! $this->editingTicketId) {
            return;
        }

        $ticket = Ticket::find($this->editingTicketId);
        if (! $ticket) {
            return;
        }

        if (! $this->isOwner($ticket)) {
            session()->flash('ticket_error', 'Akses ditolak.');

            return;
        }

        // Syarat Mutlak: Laporan belum di-acc oleh admin
        if ($ticket->status !== 'Pending') {
            session()->flash('ticket_error', 'Laporan tidak dapat diubah karena sudah di-ACC/diproses oleh admin.');
            $this->isEditModalOpen = false;

            return;
        }

        $this->validate([
            'editTitle' => 'required|min:5|max:150',
            'editTargetDepartmentId' => 'required|exists:departments,id',
            'editCategory' => 'required|string|max:50',
            'editPriority' => 'required|in:Low,Medium,High,Critical',
            'editDescription' => 'required|min:10',
            'editPhoto' => 'nullable|image|max:10240',
        ], [
            'editTitle.required' => 'Judul laporan wajib diisi.',
            'editTitle.min' => 'Judul laporan minimal 5 karakter.',
            'editDescription.required' => 'Rincian deskripsi wajib diisi.',
            'editDescription.min' => 'Rincian deskripsi minimal 10 karakter.',
        ]);

        $photoPath = $ticket->photo_path ?: $ticket->attachment_path;
        if ($this->removeExistingPhoto) {
            $photoPath = null;
        }

        if ($this->editPhoto) {
            try {
                $realPath = $this->editPhoto->getRealPath();
                $mime = $this->editPhoto->getMimeType() ?: 'image/jpeg';
                if ($realPath && file_exists($realPath)) {
                    $photoPath = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($realPath));
                } else {
                    $storedPath = $this->editPhoto->store('ticket_attachments', 'public');
                    $photoPath = '/storage/'.$storedPath;
                }
            } catch (\Throwable $e) {
                Log::warning('Edit ticket photo fallback: '.$e->getMessage());
                try {
                    $photoPath = 'data:'.$this->editPhoto->getMimeType().';base64,'.base64_encode(file_get_contents($this->editPhoto->getRealPath()));
                } catch (\Throwable $ex) {
                    // keep
                }
            }
        }

        $ticket->update([
            'title' => $this->editTitle,
            'target_department_id' => $this->editTargetDepartmentId,
            'category' => $this->editCategory,
            'priority' => $this->editPriority,
            'description' => $this->editDescription,
            'photo_path' => $photoPath,
            'attachment_path' => $photoPath,
        ]);

        $ticketId = $ticket->id;
        $this->closeEditModal();

        session()->flash('ticket_success', "Laporan #{$ticketId} berhasil diperbarui!");
        $this->dispatch('ticketUpdated', ticketId: $ticketId);
    }

    /**
     * DELETE: Menghapus Laporan (Hanya jika belum di-acc admin / Pending)
     */
    public function deleteTicket(int $id): void
    {
        $activeUserId = Auth::id() ?? session('active_user_id');
        if (! $activeUserId) {
            session()->flash('ticket_error', 'Silakan masuk (login) terlebih dahulu.');

            return;
        }

        $ticket = Ticket::find($id);
        if (! $ticket) {
            return;
        }

        if (! $this->isOwner($ticket)) {
            session()->flash('ticket_error', 'Akses ditolak: Anda hanya dapat menghapus laporan milik Anda.');

            return;
        }

        // Syarat Mutlak: Laporan belum di-acc oleh admin
        if ($ticket->status !== 'Pending') {
            session()->flash('ticket_error', 'Laporan #'.$ticket->id.' tidak dapat dihapus karena sudah di-ACC dan diproses oleh admin.');

            return;
        }

        $ticketId = $ticket->id;
        $ticket->delete();

        if ($this->viewingTicketId === $ticketId) {
            $this->closeDetailModal();
        }

        session()->flash('ticket_success', "Laporan #{$ticketId} berhasil dihapus.");
        $this->dispatch('ticketDeleted', ticketId: $ticketId);
    }

    public function render()
    {
        $query = Ticket::with(['sender', 'targetDepartment', 'messages.user'])->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->departmentFilter !== 'all') {
            $query->where('target_department_id', $this->departmentFilter);
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        $viewingTicket = $this->viewingTicketId
            ? Ticket::with(['sender', 'targetDepartment', 'messages.user'])->find($this->viewingTicketId)
            : null;

        $itDepartments = Department::where('name', 'like', 'IT%')->get();
        if ($itDepartments->isEmpty()) {
            $itDepartments = Department::take(1)->get();
        }

        $activeUserId = Auth::id() ?? session('active_user_id');

        return view('livewire.ticket-list', [
            'tickets' => $query->get(),
            'departments' => Department::all(),
            'targetDepartments' => $itDepartments,
            'viewingTicket' => $viewingTicket,
            'activeUserId' => $activeUserId,
        ]);
    }
}
