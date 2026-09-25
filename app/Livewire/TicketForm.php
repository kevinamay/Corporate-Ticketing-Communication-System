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
                $realPath = $this->photo->getRealPath();
                $mime = $this->photo->getMimeType() ?: 'image/jpeg';
                if ($realPath && file_exists($realPath)) {
                    $photoPath = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($realPath));
                } else {
                    $storedPath = $this->photo->store('ticket_attachments', 'public');
                    $photoPath = '/storage/'.$storedPath;
                }
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

    public function render()
    {
        $itDepartments = Department::where('name', 'like', 'IT%')->get();
        if ($itDepartments->isEmpty()) {
            $itDepartments = Department::take(1)->get();
        }

        return view('livewire.ticket-form', [
            'departments' => Department::all(),
            'targetDepartments' => $itDepartments,
        ]);
    }
}
