<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
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
        $activeUserId = Auth::id() ?? session('active_user_id');
        $activeUser = Auth::user() ?? ($activeUserId ? User::find($activeUserId) : null);
        $isAdmin = $activeUser && ($activeUser->role === 'admin' || $activeUser->email === 'user123@gmail.com');

        if ($initialTicketId) {
            $t = Ticket::find($initialTicketId);
            $isOwner = $t && ((int) $t->user_id === (int) $activeUserId || (int) $t->sender_id === (int) $activeUserId);
            if ($isAdmin || $isOwner) {
                $this->ticketId = $initialTicketId;
            }
        } else {
            $query = Ticket::latest();
            if (! $isAdmin) {
                if ($activeUserId) {
                    $query->where(function ($q) use ($activeUserId) {
                        $q->where('user_id', $activeUserId)->orWhere('sender_id', $activeUserId);
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            $this->ticketId = $query->first()?->id;
        }
    }

    #[On('ticketCreated')]
    public function onTicketCreated(int $ticketId): void
    {
        $this->ticketId = $ticketId;
    }

    #[On('ticketSelected')]
    public function onTicketSelected(int $ticketId): void
    {
        $activeUserId = Auth::id() ?? session('active_user_id');
        $activeUser = Auth::user() ?? ($activeUserId ? User::find($activeUserId) : null);
        $isAdmin = $activeUser && ($activeUser->role === 'admin' || $activeUser->email === 'user123@gmail.com');

        $t = Ticket::find($ticketId);
        $isOwner = $t && ((int) $t->user_id === (int) $activeUserId || (int) $t->sender_id === (int) $activeUserId);

        if ($isAdmin || $isOwner) {
            $this->ticketId = $ticketId;
            $this->isCalling = false;
            $this->callSeconds = 0;
        }
    }

    #[On('ticketDeleted')]
    public function onTicketDeleted(int $ticketId): void
    {
        if ($this->ticketId === $ticketId) {
            $activeUserId = Auth::id() ?? session('active_user_id');
            $activeUser = Auth::user() ?? ($activeUserId ? User::find($activeUserId) : null);
            $isAdmin = $activeUser && ($activeUser->role === 'admin' || $activeUser->email === 'user123@gmail.com');

            $query = Ticket::latest();
            if (! $isAdmin) {
                if ($activeUserId) {
                    $query->where(function ($q) use ($activeUserId) {
                        $q->where('user_id', $activeUserId)->orWhere('sender_id', $activeUserId);
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            $this->ticketId = $query->first()?->id;
            $this->isCalling = false;
            $this->callSeconds = 0;
        }
    }

    public function selectTicket(int $id): void
    {
        $activeUserId = Auth::id() ?? session('active_user_id');
        $activeUser = Auth::user() ?? ($activeUserId ? User::find($activeUserId) : null);
        $isAdmin = $activeUser && ($activeUser->role === 'admin' || $activeUser->email === 'user123@gmail.com');

        $t = Ticket::find($id);
        $isOwner = $t && ((int) $t->user_id === (int) $activeUserId || (int) $t->sender_id === (int) $activeUserId);

        if ($isAdmin || $isOwner) {
            $this->ticketId = $id;
        }
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

        $ticket = Ticket::find($this->ticketId);
        if (! $ticket) {
            return;
        }

        $activeUser = Auth::user() ?? ($activeUserId ? User::find($activeUserId) : null);
        $isAdmin = $activeUser && ($activeUser->role === 'admin' || $activeUser->email === 'user123@gmail.com');
        $isOwner = (int) $ticket->user_id === (int) $activeUserId || (int) $ticket->sender_id === (int) $activeUserId;

        if (! $isAdmin && ! $isOwner) {
            session()->flash('error', 'Akses ditolak: Anda tidak memiliki akses ke tiket ini.');

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

        $activeUserId = Auth::id() ?? session('active_user_id');
        $activeUser = Auth::user() ?? ($activeUserId ? User::find($activeUserId) : null);
        $isAdmin = $activeUser && ($activeUser->role === 'admin' || $activeUser->email === 'user123@gmail.com');

        $ticket = Ticket::find($this->ticketId);
        if ($ticket) {
            $isOwner = (int) $ticket->user_id === (int) $activeUserId || (int) $ticket->sender_id === (int) $activeUserId;
            if ($isAdmin || $isOwner) {
                $ticket->update(['status' => $status]);
            }
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
        $activeUserId = Auth::id() ?? session('active_user_id');
        $activeUser = Auth::user() ?? ($activeUserId ? User::find($activeUserId) : null);
        $isAdmin = $activeUser && ($activeUser->role === 'admin' || $activeUser->email === 'user123@gmail.com');

        $ticket = null;
        if ($this->ticketId) {
            $t = Ticket::with(['sender', 'targetDepartment', 'messages.user'])->find($this->ticketId);
            if ($t) {
                $isOwner = (int) $t->user_id === (int) $activeUserId || (int) $t->sender_id === (int) $activeUserId;
                if ($isAdmin || $isOwner) {
                    $ticket = $t;
                }
            }
        }

        return view('livewire.ticket-chat', [
            'ticket' => $ticket,
            'activeUserId' => $activeUserId,
        ]);
    }
}
