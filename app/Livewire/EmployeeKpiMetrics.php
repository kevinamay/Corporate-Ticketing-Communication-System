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
        $activeUserId = auth()->id() ?? session('active_user_id');

        $baseQuery = Ticket::query();
        if ($activeUserId) {
            $baseQuery->where(function ($q) use ($activeUserId) {
                $q->where('user_id', $activeUserId)->orWhere('sender_id', $activeUserId);
            });
        } else {
            $baseQuery->whereRaw('1 = 0');
        }

        return view('livewire.employee-kpi-metrics', [
            'activeCount' => (clone $baseQuery)->where('status', '!=', 'Resolved')->count(),
            'pendingCount' => (clone $baseQuery)->whereIn('status', ['Pending', 'Open'])->count(),
            'inProgressCount' => (clone $baseQuery)->where('status', 'In Progress')->count(),
            'resolvedCount' => (clone $baseQuery)->where('status', 'Resolved')->count(),
        ]);
    }
}
