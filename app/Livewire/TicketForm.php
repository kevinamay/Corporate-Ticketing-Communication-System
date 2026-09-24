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

    public string $category = 'IT';

    public ?int $sender_department_id = null;

    public ?int $target_department_id = null;

    public string $priority = 'Medium';

    public string $status = 'Pending';

    public string $description = '';

    public $photo = null;

    public bool $isSuccess = false;

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'title' => 'required|min:5|max:150',
            'sender_department_id' => 'required|exists:departments,id',
            'target_department_id' => 'required|exists:departments,id',
            'category' => 'required|in:IT,HR,Maintenance,General',
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
    }

    public function setPriority(string $level): void
    {
        $this->priority = $level;
    }

    public function setCategory(string $cat): void
    {
        $this->category = $cat;
        // Auto match target department if possible
        if ($cat === 'IT') {
            $dept = Department::where('name', 'like', '%IT%')->first();
            if ($dept) {
                $this->target_department_id = $dept->id;
            }
        } elseif ($cat === 'HR') {
            $dept = Department::where('name', 'like', '%Human%')->first();
            if ($dept) {
                $this->target_department_id = $dept->id;
            }
        } elseif ($cat === 'Maintenance') {
            $dept = Department::where('name', 'like', '%Facility%')->first();
            if ($dept) {
                $this->target_department_id = $dept->id;
            }
        }
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
            'sender_id' => $currentUserId,
            'target_department_id' => $this->target_department_id,
            'title' => $this->title,
            'category' => $this->category,
            'description' => $this->description,
            'photo_path' => $photoPath,
            'priority' => $this->priority,
            'status' => $this->status,
        ]);

        $this->reset(['title', 'description', 'photo']);
        $this->photo = null;
        $this->priority = 'Medium';
        $this->status = 'Pending';
        $this->isSuccess = true;

        $this->dispatch('ticketCreated', ticketId: $ticket->id);
    }

    public function render()
    {
        return view('livewire.ticket-form', [
            'departments' => Department::all(),
        ]);
    }
}
