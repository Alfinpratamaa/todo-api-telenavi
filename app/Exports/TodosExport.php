<?php

namespace App\Exports;

use App\Models\Todo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TodosExport implements FromCollection, WithHeadings, WithMapping
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Todo::query();

        $query->when(
            $this->request->input('title'),
            fn($q, $title) => $q->where('title', 'like', "%{$title}%")
        );

        $query->when(
            $this->request->input('assignee'),
            fn($q, $assignee) => $q->whereIn('assignee', explode(',', $assignee))
        );

        $query->when(
            $this->request->input('status'),
            fn($q, $status) => $q->whereIn('status', explode(',', $status))
        );

        $query->when(
            $this->request->input('priority'),
            fn($q, $priority) => $q->whereIn('priority', explode(',', $priority))
        );



        $query->when(
            $this->request->input('due_date_start'),
            fn($q, $date) => $q->where('due_date', '>=', $date)
        );
        $query->when(
            $this->request->input('due_date_end'),
            fn($q, $date) => $q->where('due_date', '<=', $date)
        );

        $query->when(
            $this->request->input('time_tracked_min'),
            fn($q, $min) => $q->where('time_tracked', '>=', $min)
        );
        $query->when(
            $this->request->input('time_tracked_max'),
            fn($q, $max) => $q->where('time_tracked', '<=', $max)
        );

        $todos = $query->get();

        if ($todos->isNotEmpty()) {
            $summary = new \stdClass();
            $summary->is_summary = true;
            $summary->total_todos = $todos->count();
            $summary->total_time_tracked = $todos->sum('time_tracked');
            $todos->push($summary);
        }

        return $todos;
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked (minutes)',
            'Status',
            'Priority',
        ];
    }

    public function map($todo): array
    {
        if (isset($todo->is_summary)) {
            return [
                'TOTAL',
                '',
                '',
                $todo->total_time_tracked,
                "{$todo->total_todos} Todos",
                '',
            ];
        }

        return [
            $todo->title,
            $todo->assignee,
            $todo->due_date->format('Y-m-d'),
            $todo->time_tracked,
            ucfirst($todo->status),
            ucfirst($todo->priority),
        ];
    }
}
