<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Attributes\On;
use Livewire\Component;

class EmployeeKpiMetrics extends Component
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
        return view('livewire.employee-kpi-metrics', [
            'activeCount' => Ticket::where('status', '!=', 'Resolved')->count(),
            'pendingCount' => Ticket::whereIn('status', ['Pending', 'Open'])->count(),
            'inProgressCount' => Ticket::where('status', 'In Progress')->count(),
            'resolvedCount' => Ticket::where('status', 'Resolved')->count(),
        ]);
    }
}
