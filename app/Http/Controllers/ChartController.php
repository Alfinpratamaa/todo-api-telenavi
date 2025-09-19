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
        $type = $request->get('type', 'status');

        return match ($type) {
            'status' => $this->getStatusChart(),
            'priority' => $this->getPriorityChart(),
            'assignee' => $this->getAssigneeChart(),
            default => response()->json(['error' => 'Invalid chart type'], 400)
        };
    }

    private function getStatusChart(): JsonResponse
    {
        $data = Todo::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        return response()->json($data);
    }

    private function getPriorityChart(): JsonResponse
    {
        $data = Todo::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->get()
            ->pluck('total', 'priority');

        return response()->json($data);
    }

    private function getAssigneeChart(): JsonResponse
    {
        $data = Todo::select('assignee')
            ->selectRaw('COUNT(*) as total_todos')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_todos', ['pending'])
            ->selectRaw('SUM(CASE WHEN status = ? THEN time_tracked ELSE 0 END) as total_time_tracked_completed', ['completed'])
            ->whereNotNull('assignee')
            ->groupBy('assignee')
            ->get();

        return response()->json([
            'type' => 'assignee',
            'data' => $data
        ]);
    }
}
