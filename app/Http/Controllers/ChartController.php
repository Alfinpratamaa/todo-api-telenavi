<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['sometimes', 'in:status,priority,assignee']
        ]);

        $type = $request->get('type', 'status');
        $data = match ($type) {
            'status'   => $this->getStatusChartData(),
            'priority' => $this->getPriorityChartData(),
            'assignee' => $this->getAssigneeChartData(),
        };

        return response()->json($data);
    }

    private function getStatusChartData(): array
    {
        $data = Todo::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        return ['status_summary' => $data];
    }
    private function getPriorityChartData(): array
    {
        $data = Todo::query()
            ->select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->get()
            ->pluck('total', 'priority');

        return ['priority_summary' => $data];
    }
    private function getAssigneeChartData(): array
    {
        $data = Todo::query()
            ->select(
                'assignee',
                DB::raw('COUNT(*) as total_todos'),
                DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as total_pending_todos"),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN time_tracked ELSE 0 END) as timetracked_completed_todos")
            )
            ->whereNotNull('assignee')
            ->groupBy('assignee')
            ->get();

        $transformedData = $data->mapWithKeys(function ($item) {
            return [
                $item->assignee => [
                    'total_todos' => (int) $item->total_todos,
                    'total_pending_todos' => (int) $item->total_pending_todos,
                    'timetracked_completed_todos' => (int) $item->timetracked_completed_todos,
                ]
            ];
        });

        return ['assignee_summary' => $transformedData];
    }
}
