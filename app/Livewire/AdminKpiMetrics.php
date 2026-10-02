<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Attributes\On;
use Livewire\Component;

class AdminKpiMetrics extends Component
{
    #[On('ticketCreated')]
    #[On('ticketStatusUpdated')]
    #[On('ticketDeleted')]
    #[On('ticketUpdated')]
    public function refreshMetrics(): void
    {
        // Automatically re-renders KPI cards upon any ticket changes
    }

    public function render()
    {
        return view('livewire.admin-kpi-metrics', [
            'activeCount' => Ticket::where('status', '!=', 'Resolved')->count(),
            'pendingCount' => Ticket::where('status', 'Pending')->count(),
            'inProgressCount' => Ticket::where('status', 'In Progress')->count(),
            'resolvedCount' => Ticket::where('status', 'Resolved')->count(),
        ]);
    }
}
