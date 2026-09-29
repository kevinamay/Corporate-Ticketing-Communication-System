<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Ticket;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class TicketDailyChart extends Component
{
    public int $days = 14;

    public string $departmentFilter = 'all';

    public string $chartType = 'line'; // 'line' or 'bar'

    public function setDays(int $days): void
    {
        $this->days = in_array($days, [7, 14, 30]) ? $days : 14;
    }

    public function setChartType(string $type): void
    {
        $this->chartType = in_array($type, ['line', 'bar']) ? $type : 'line';
    }

    #[On('ticketCreated')]
    #[On('ticketStatusUpdated')]
    #[On('ticketDeleted')]
    public function refreshChart(): void
    {
        // Re-renders automatically on Livewire events
    }

    public function render(): View
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($this->days - 1);

        $query = Ticket::query()
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($this->departmentFilter !== 'all') {
            $query->where('target_department_id', (int) $this->departmentFilter);
        }

        $tickets = $query->get(['id', 'created_at', 'status', 'priority', 'target_department_id']);

        $grouped = $tickets->groupBy(function ($ticket) {
            return $ticket->created_at ? $ticket->created_at->format('Y-m-d') : '';
        });

        $period = CarbonPeriod::create($startDate, $endDate);
        $dataPoints = [];
        $totalCount = 0;
        $maxCount = 0;
        $peakDate = '-';
        $peakCount = 0;
        $resolvedCount = 0;
        $pendingCount = 0;
        $inProgressCount = 0;

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayTickets = $grouped->get($dateStr, collect());
            $count = $dayTickets->count();
            $dayResolved = $dayTickets->where('status', 'Resolved')->count();
            $dayPending = $dayTickets->where('status', 'Pending')->count();
            $dayInProgress = $dayTickets->whereIn('status', ['In Progress', 'Open'])->count();

            $totalCount += $count;
            $resolvedCount += $dayResolved;
            $pendingCount += $dayPending;
            $inProgressCount += $dayInProgress;

            if ($count > $peakCount) {
                $peakCount = $count;
                $peakDate = $date->translatedFormat('d M');
            }

            if ($count > $maxCount) {
                $maxCount = $count;
            }

            $dataPoints[] = [
                'date' => $dateStr,
                'label' => $date->translatedFormat('d M'),
                'dayName' => $date->translatedFormat('D'),
                'fullDate' => $date->translatedFormat('l, d F Y'),
                'count' => $count,
                'resolved' => $dayResolved,
                'pending' => $dayPending,
                'in_progress' => $dayInProgress,
            ];
        }

        // Determine Y-axis max scale (minimum 5, matching standard CSAT / Jira ticket charts)
        if ($maxCount <= 5) {
            $yMax = 5;
            $gridSteps = [5, 4, 3, 2, 1, 0];
        } elseif ($maxCount <= 10) {
            $yMax = 10;
            $gridSteps = [10, 8, 6, 4, 2, 0];
        } elseif ($maxCount <= 20) {
            $yMax = 20;
            $gridSteps = [20, 15, 10, 5, 0];
        } else {
            $yMax = (int) (ceil(($maxCount * 1.15) / 5) * 5);
            $step = $yMax / 4;
            $gridSteps = [
                $yMax,
                (int) round($step * 3),
                (int) round($step * 2),
                (int) round($step * 1),
                0,
            ];
        }

        // SVG Coordinate mapping
        $chartLeft = 45;
        $chartRight = 780;
        $chartTop = 25;
        $chartBottom = 205;
        $chartWidth = $chartRight - $chartLeft; // 735
        $chartHeight = $chartBottom - $chartTop; // 180

        $totalPoints = count($dataPoints);
        $linePathCoords = [];
        $areaPathCoords = [];

        foreach ($dataPoints as $index => &$pt) {
            $x = $totalPoints > 1
                ? $chartLeft + ($index / ($totalPoints - 1)) * $chartWidth
                : $chartLeft + ($chartWidth / 2);

            $y = $chartBottom - ($pt['count'] / $yMax) * $chartHeight;

            $pt['x'] = round($x, 1);
            $pt['y'] = round($y, 1);

            $barWidth = max(10, min(36, ($chartWidth / $totalPoints) * 0.65));
            $pt['barWidth'] = round($barWidth, 1);
            $pt['barX'] = round($x - ($barWidth / 2), 1);
            $pt['barHeight'] = round(($pt['count'] / $yMax) * $chartHeight, 1);
            $pt['barY'] = round($chartBottom - $pt['barHeight'], 1);

            $linePathCoords[] = "{$pt['x']},{$pt['y']}";
        }
        unset($pt);

        $linePath = ! empty($linePathCoords) ? 'M '.implode(' L ', $linePathCoords) : '';
        $firstX = $dataPoints[0]['x'] ?? $chartLeft;
        $lastX = $dataPoints[count($dataPoints) - 1]['x'] ?? $chartRight;
        $areaPath = ! empty($linePathCoords)
            ? 'M '.implode(' L ', $linePathCoords)." L {$lastX},{$chartBottom} L {$firstX},{$chartBottom} Z"
            : '';

        // Grid lines calculation
        $gridLines = [];
        foreach ($gridSteps as $val) {
            $gridY = $chartBottom - ($val / $yMax) * $chartHeight;
            $gridLines[] = [
                'val' => $val,
                'y' => round($gridY, 1),
            ];
        }

        $dailyAverage = round($totalCount / $this->days, 1);
        $resolutionRate = $totalCount > 0 ? (int) round(($resolvedCount / $totalCount) * 100) : 0;

        return view('livewire.ticket-daily-chart', [
            'dataPoints' => $dataPoints,
            'gridLines' => $gridLines,
            'linePath' => $linePath,
            'areaPath' => $areaPath,
            'totalCount' => $totalCount,
            'dailyAverage' => $dailyAverage,
            'peakDate' => $peakDate,
            'peakCount' => $peakCount,
            'resolvedCount' => $resolvedCount,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
            'resolutionRate' => $resolutionRate,
            'departments' => Department::all(),
            'chartLeft' => $chartLeft,
            'chartRight' => $chartRight,
            'chartBottom' => $chartBottom,
            'yMax' => $yMax,
        ]);
    }
}
