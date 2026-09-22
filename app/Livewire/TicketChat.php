<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class TicketChat extends Component
{
    public ?int $ticketId = null;

    public string $newMessage = '';

    public bool $isCalling = false;

    public int $callSeconds = 0;

    public function mount(?int $initialTicketId = null): void
    {
        if ($initialTicketId) {
            $this->ticketId = $initialTicketId;
        } else {
            // Default to first active ticket
            $firstTicket = Ticket::latest()->first();
            $this->ticketId = $firstTicket?->id;
        }
    }

    #[On('ticketSelected')]
    public function onTicketSelected(int $ticketId): void
    {
        $this->ticketId = $ticketId;
        $this->resetErrorBag();
        $this->newMessage = '';
        $this->isCalling = false;
    }

    #[On('ticketCreated')]
    public function onTicketCreated(int $ticketId): void
    {
        $this->ticketId = $ticketId;
        $this->resetErrorBag();
        $this->newMessage = '';
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|min:1|max:1000',
        ]);

        if (! $this->ticketId) {
            return;
        }

        $activeUserId = session('active_user_id', User::first()?->id ?? 1);

        Message::create([
            'ticket_id' => $this->ticketId,
            'user_id' => $activeUserId,
            'message' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
        $this->dispatch('messageSent');
    }

    public function updateStatus(string $status): void
    {
        if (! $this->ticketId) {
            return;
        }

        $ticket = Ticket::find($this->ticketId);
        if ($ticket) {
            $ticket->update(['status' => $status]);
        }
    }

    public function startCall(): void
    {
        $this->isCalling = true;
        $this->callSeconds = 0;
    }

    public function endCall(): void
    {
        $this->isCalling = false;
        $this->callSeconds = 0;
    }

    public function render()
    {
        $ticket = $this->ticketId
            ? Ticket::with(['sender', 'targetDepartment', 'messages.user'])->find($this->ticketId)
            : null;

        $activeUserId = session('active_user_id', User::first()?->id ?? 1);

        return view('livewire.ticket-chat', [
            'ticket' => $ticket,
            'activeUserId' => $activeUserId,
        ]);
    }
}
