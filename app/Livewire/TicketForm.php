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
            'status' => 'required|in:Pending,Open,In Progress,Resolved',
            'description' => 'required|min:10',
            'photo' => 'nullable|image|max:10240',
        ];
    }

    public function mount(): void
    {
        $depts = Department::all();
        if ($depts->isNotEmpty()) {
            $this->sender_department_id = $depts->first()->id;
            $this->target_department_id = $depts->count() > 1 ? $depts->get(1)->id : $depts->first()->id;
        }

        // Align with active user's department if available
        $currentUserId = Auth::id() ?? session('active_user_id');
        if ($currentUserId) {
            $currentUser = User::find($currentUserId);
            if ($currentUser && $currentUser->department_id) {
                $this->sender_department_id = $currentUser->department_id;
            }
        }

        // Initialize category based on current target department
        $categories = $this->availableCategories;
        $this->category = $categories[0] ?? 'Other';
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
     * Get categories list dynamically based on selected target department.
     */
    public function getAvailableCategoriesProperty(): array
    {
        if (! $this->target_department_id) {
            return $this->departmentCategories['General'] ?? ['Other'];
        }

        $dept = Department::find($this->target_department_id);
        if (! $dept) {
            return $this->departmentCategories['General'] ?? ['Other'];
        }

        $deptKey = 'General';
        if (str_contains($dept->name, 'IT')) {
            $deptKey = 'IT';
        } elseif (str_contains($dept->name, 'Human') || str_contains($dept->name, 'HR')) {
            $deptKey = 'HR';
        } elseif (str_contains($dept->name, 'Facility') || str_contains($dept->name, 'Maintenance')) {
            $deptKey = 'Maintenance';
        } elseif (isset($this->departmentCategories[$dept->name])) {
            $deptKey = $dept->name;
        }

        return $this->departmentCategories[$deptKey] ?? ['Other'];
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
            'status' => $this->status,
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
     * Delete user ticket and refresh view.
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

        if ($ticket) {
            $ticket->delete();
            session()->flash('ticket_deleted', 'Tiket berhasil dihapus.');
            $this->dispatch('ticketDeleted', ticketId: $id);
        }
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

        return view('livewire.ticket-form', [
            'departments' => Department::all(),
            'myTickets' => $myTickets,
        ]);
    }
}
