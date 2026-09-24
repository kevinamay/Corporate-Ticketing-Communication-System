<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Ticket;
use Livewire\Attributes\On;
use Livewire\Component;

class TicketList extends Component
{
    public ?int $selectedTicketId = null;

    public string $statusFilter = 'all';

    public string $departmentFilter = 'all';

    public string $search = '';

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
            if ($this->selectedTicketId) {
                $this->dispatch('ticketSelected', ticketId: $this->selectedTicketId);
            }
        }
    }

    #[On('ticketUpdated')]
    public function onTicketUpdated(int $ticketId): void
    {
        // Re-renders view and keeps selected ticket
    }

    public function selectTicket(int $id): void
    {
        $this->selectedTicketId = $id;
        $this->dispatch('ticketSelected', ticketId: $id);
    }

    public function render()
    {
        $query = Ticket::with(['sender', 'targetDepartment', 'messages'])->latest();

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

        return view('livewire.ticket-list', [
            'tickets' => $query->get(),
            'departments' => Department::all(),
        ]);
    }
}
