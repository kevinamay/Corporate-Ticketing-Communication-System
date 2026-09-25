<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketForm extends Component
{
    use WithFileUploads;

    public string $title = '';

    public ?int $sender_department_id = null;

    public ?int $target_department_id = null;

    public string $category = '';

    public string $priority = 'Medium';

    public string $status = 'Pending';

    public string $description = '';

    public $photo = null;

    public bool $isSuccess = false;

    // Detail Modal State (Read)
    public bool $isDetailModalOpen = false;

    public ?int $viewingTicketId = null;

    // Edit Ticket Modal State (Update)
    public bool $isEditModalOpen = false;

    public ?int $editingTicketId = null;

    public string $editTitle = '';

    public ?int $editTargetDepartmentId = null;

    public string $editCategory = '';

    public string $editPriority = 'Medium';

    public string $editStatus = 'Pending';

    public string $editDescription = '';

    public $editPhoto = null;

    public ?string $existingPhotoUrl = null;

    public bool $removeExistingPhoto = false;

    /**
     * Exact department to categories mapping.
     */
    public array $departmentCategories = [
        'IT' => ['Network', 'Hardware', 'Software', 'Account', 'Other'],
        'HR' => ['Payroll', 'Leave', 'Attendance', 'Other'],
        'Maintenance' => ['AC', 'Electrical', 'Plumbing', 'Other'],
        'General' => ['Supplies', 'Meeting Room', 'Transport', 'Other'],
    ];

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'title' => 'required|min:5|max:150',
            'sender_department_id' => 'required|exists:departments,id',
            'target_department_id' => 'required|exists:departments,id',
            'category' => 'required|string|max:50',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'description' => 'required|min:10',
            'photo' => 'nullable|image|max:10240',
        ];
    }

    public function mount(): void
    {
        $depts = Department::all();
        $itDept = Department::where('name', 'like', 'IT%')->first() ?? $depts->first();

        if ($depts->isNotEmpty()) {
            $this->sender_department_id = $depts->first()->id;
        }

        if ($itDept) {
            $this->target_department_id = $itDept->id;
        }

        // Align with active user's department if available
        $currentUserId = Auth::id() ?? session('active_user_id');
        if ($currentUserId) {
            $currentUser = User::find($currentUserId);
            if ($currentUser && $currentUser->department_id) {
                $this->sender_department_id = $currentUser->department_id;
            }
        }

        // Initialize category strictly to IT categories
        $categories = $this->availableCategories;
        $this->category = $categories[0] ?? 'Network';
    }

    /**
     * Dependent dropdown hook when target_department_id changes.
     */
    public function updatedTargetDepartmentId($value): void
    {
        $categories = $this->availableCategories;
        $this->category = $categories[0] ?? 'Other';
    }

    /**
     * Get categories list dynamically based on IT Support department.
     */
    public function getAvailableCategoriesProperty(): array
    {
        return $this->departmentCategories['IT'] ?? ['Network', 'Hardware', 'Software', 'Account', 'Other'];
    }

    public function setPriority(string $level): void
    {
        $this->priority = $level;
    }

    public function removePhoto(): void
    {
        $this->photo = null;
    }

    public function submit(): void
    {
        $currentUserId = Auth::id() ?? session('active_user_id');

        if (! $currentUserId) {
            session()->flash('error', 'Silakan masuk (login) terlebih dahulu untuk membuat tiket.');
            $this->redirect(route('login'));

            return;
        }

        $this->validate();

        $photoPath = null;
        if ($this->photo) {
            try {
                $storedPath = $this->photo->store('ticket_attachments', 'public');
                $photoPath = '/storage/'.$storedPath;
            } catch (\Throwable $e) {
                Log::warning('Ticket photo store fallback: '.$e->getMessage());
                try {
                    $photoPath = 'data:'.$this->photo->getMimeType().';base64,'.base64_encode(file_get_contents($this->photo->getRealPath()));
                } catch (\Throwable $ex) {
                    $photoPath = null;
                }
            }
        }

        $ticket = Ticket::create([
            'user_id' => $currentUserId,
            'sender_id' => $currentUserId,
            'target_department_id' => $this->target_department_id,
            'title' => $this->title,
            'category' => $this->category,
            'description' => $this->description,
            'photo_path' => $photoPath,
            'attachment_path' => $photoPath,
            'priority' => $this->priority,
            'status' => 'Pending',
        ]);

        $this->reset(['title', 'description', 'photo']);
        $this->photo = null;
        $this->priority = 'Medium';
        $this->status = 'Pending';
        $this->category = $this->availableCategories[0] ?? 'Other';
        $this->isSuccess = true;

        $this->dispatch('ticketCreated', ticketId: $ticket->id);
    }

    /**
     * READ: Open ticket detail modal.
     */
    public function viewTicket(int $id): void
    {
        $this->viewingTicketId = $id;
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal(): void
    {
        $this->isDetailModalOpen = false;
        $this->viewingTicketId = null;
    }

    /**
     * Delete user ticket and refresh view (Only if Pending / Belum di-acc admin).
     */
    public function deleteTicket(int $id): void
    {
        $currentUserId = Auth::id() ?? session('active_user_id');
        if (! $currentUserId) {
            return;
        }

        $ticket = Ticket::where('id', $id)
            ->where(function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId)
                      ->orWhere('sender_id', $currentUserId);
            })
            ->first();

        if (! $ticket) {
            return;
        }

        // Syarat Mutlak: Laporan belum di-acc oleh admin
        if ($ticket->status !== 'Pending') {
            session()->flash('ticket_error', 'Laporan #'.$ticket->id.' tidak dapat dihapus karena sudah di-ACC dan diproses oleh admin.');

            return;
        }

        $ticket->delete();
        if ($this->viewingTicketId === $id) {
            $this->closeDetailModal();
        }

        session()->flash('ticket_deleted', 'Laporan berhasil dihapus.');
        $this->dispatch('ticketDeleted', ticketId: $id);
    }

    /**
     * Open the edit modal with the selected ticket data (Only if Pending / Belum di-acc admin).
     */
    public function openEditModal(int $id): void
    {
        $currentUserId = Auth::id() ?? session('active_user_id');
        if (! $currentUserId) {
            return;
        }

        $ticket = Ticket::where('id', $id)
            ->where(function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId)
                      ->orWhere('sender_id', $currentUserId);
            })
            ->first();

        if (! $ticket) {
            return;
        }

        // Syarat Mutlak: Laporan belum di-acc oleh admin
        if ($ticket->status !== 'Pending') {
            session()->flash('ticket_error', 'Laporan ini sudah di-ACC/diproses oleh admin sehingga tidak dapat diubah lagi.');

            return;
        }

        $this->editingTicketId = $ticket->id;
        $this->editTitle = $ticket->title;
        $this->editTargetDepartmentId = $ticket->target_department_id;
        $this->editCategory = $ticket->category;
        $this->editPriority = $ticket->priority;
        $this->editStatus = $ticket->status;
        $this->editDescription = $ticket->description;
        $this->existingPhotoUrl = $ticket->photo_url;
        $this->editPhoto = null;
        $this->removeExistingPhoto = false;

        $this->isDetailModalOpen = false;
        $this->isEditModalOpen = true;
    }

    /**
     * Close the edit modal and reset edit state.
     */
    public function closeEditModal(): void
    {
        $this->isEditModalOpen = false;
        $this->editingTicketId = null;
        $this->editPhoto = null;
        $this->removeExistingPhoto = false;
        $this->resetValidation();
    }

    /**
     * Dependent dropdown hook when edit target department changes.
     */
    public function updatedEditTargetDepartmentId($value): void
    {
        $categories = $this->editAvailableCategories;
        $this->editCategory = $categories[0] ?? 'Other';
    }

    /**
     * Get available categories dynamically for the edit modal (IT Support).
     */
    public function getEditAvailableCategoriesProperty(): array
    {
        return $this->departmentCategories['IT'] ?? ['Network', 'Hardware', 'Software', 'Account', 'Other'];
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

    /**
     * Update the ticket in the database.
     */
    public function updateTicket(): void
    {
        $currentUserId = Auth::id() ?? session('active_user_id');
        if (! $currentUserId || ! $this->editingTicketId) {
            return;
        }

        $this->validate([
            'editTitle' => 'required|min:5|max:150',
            'editTargetDepartmentId' => 'required|exists:departments,id',
            'editCategory' => 'required|string|max:50',
            'editPriority' => 'required|in:Low,Medium,High,Critical',
            'editDescription' => 'required|min:10',
            'editPhoto' => 'nullable|image|max:10240',
        ]);

        $ticket = Ticket::where('id', $this->editingTicketId)
            ->where(function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId)
                      ->orWhere('sender_id', $currentUserId);
            })
            ->first();

        if (! $ticket) {
            return;
        }

        // Syarat Mutlak: Laporan belum di-acc oleh admin
        if ($ticket->status !== 'Pending') {
            session()->flash('ticket_error', 'Laporan tidak dapat diubah karena sudah di-ACC dan diproses oleh admin.');
            $this->isEditModalOpen = false;

            return;
        }

        $photoPath = $ticket->photo_path ?: $ticket->attachment_path;
        if ($this->removeExistingPhoto) {
            $photoPath = null;
        }

        if ($this->editPhoto) {
            try {
                $storedPath = $this->editPhoto->store('ticket_attachments', 'public');
                $photoPath = '/storage/'.$storedPath;
            } catch (\Throwable $e) {
                Log::warning('Edit ticket photo store fallback: '.$e->getMessage());
                try {
                    $photoPath = 'data:'.$this->editPhoto->getMimeType().';base64,'.base64_encode(file_get_contents($this->editPhoto->getRealPath()));
                } catch (\Throwable $ex) {
                    // keep current
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

        $this->isEditModalOpen = false;
        $this->editingTicketId = null;
        $this->editPhoto = null;
        $this->removeExistingPhoto = false;

        session()->flash('ticket_updated', 'Tiket #'.$ticketId.' berhasil diperbarui.');
        $this->dispatch('ticketUpdated', ticketId: $ticketId);
    }

    public function render()
    {
        $currentUserId = Auth::id() ?? session('active_user_id');
        $myTickets = $currentUserId
            ? Ticket::where(function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId)
                      ->orWhere('sender_id', $currentUserId);
            })->with('targetDepartment')->latest()->get()
            : collect();

        $itDepartments = Department::where('name', 'like', 'IT%')->get();
        if ($itDepartments->isEmpty()) {
            $itDepartments = Department::take(1)->get();
        }

        return view('livewire.ticket-form', [
            'departments' => Department::all(),
            'targetDepartments' => $itDepartments,
            'myTickets' => $myTickets,
        ]);
    }
}
