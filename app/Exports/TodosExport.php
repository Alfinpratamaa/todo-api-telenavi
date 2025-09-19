<?php

// app/Exports/TodosExport.php
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

        // Terapkan filter berdasarkan request
        $query->when($this->request->input('title'), fn($q, $title) => $q->where('title', 'like', "%{$title}%"));
        $query->when($this->request->input('assignee'), fn($q, $assignee) => $q->where('assignee', $assignee));
        $query->when($this->request->input('status'), fn($q, $status) => $q->where('status', $status));
        $query->when($this->request->input('priority'), fn($q, $priority) => $q->where('priority', $priority));

        // Filter range tanggal
        $query->when($this->request->input('due_date_start'), fn($q, $date) => $q->where('due_date', '>=', $date));
        $query->when($this->request->input('due_date_end'), fn($q, $date) => $q->where('due_date', '<=', $date));

        // Filter range time_tracked
        $query->when($this->request->input('time_tracked_min'), fn($q, $min) => $q->where('time_tracked', '>=', $min));
        $query->when($this->request->input('time_tracked_max'), fn($q, $max) => $q->where('time_tracked', '<=', $max));

        // Dapatkan data utama
        $todos = $query->get();

        // Buat summary row
        if ($todos->isNotEmpty()) {
            $totalTimeTracked = $todos->sum('time_tracked');
            $totalTodos = $todos->count();

            // Tambahkan objek kosong sebagai baris summary
            $summary = new \stdClass();
            $summary->is_summary = true;
            $summary->total_todos = $totalTodos;
            $summary->total_time_tracked = $totalTimeTracked;
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
        // Cek jika ini adalah baris summary
        if (isset($todo->is_summary)) {
            return [
                'TOTAL', // Kolom Title
                '', // Kolom Assignee
                '', // Kolom Due Date
                $todo->total_time_tracked, // Kolom Time Tracked
                "{$todo->total_todos} Todos", // Kolom Status
                '', // Kolom Priority
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
