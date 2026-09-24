<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
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
        $this->ticketId = $initialTicketId ?? Ticket::latest()->first()?->id;
    }

    #[On('ticketCreated')]
    public function onTicketCreated(int $ticketId): void
    {
        $this->ticketId = $ticketId;
    }

    #[On('ticketSelected')]
    public function onTicketSelected(int $ticketId): void
    {
        $this->ticketId = $ticketId;
        $this->isCalling = false;
        $this->callSeconds = 0;
    }

    #[On('ticketDeleted')]
    public function onTicketDeleted(int $ticketId): void
    {
        if ($this->ticketId === $ticketId) {
            $this->ticketId = Ticket::latest()->first()?->id;
            $this->isCalling = false;
            $this->callSeconds = 0;
        }
    }

    public function selectTicket(int $id): void
    {
        $this->ticketId = $id;
    }

    public function sendMessage(): void
    {
        $activeUserId = Auth::id() ?? session('active_user_id');

        if (! $activeUserId) {
            session()->flash('error', 'Silakan masuk (login) terlebih dahulu.');
            $this->redirect(route('login'));

            return;
        }

        $this->validate([
            'newMessage' => 'required|min:1|max:1000',
        ]);

        if (! $this->ticketId) {
            return;
        }

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

        $activeUserId = Auth::id() ?? session('active_user_id');

        return view('livewire.ticket-chat', [
            'ticket' => $ticket,
            'activeUserId' => $activeUserId,
        ]);
    }
}
