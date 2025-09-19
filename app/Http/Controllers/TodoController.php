<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Models\Todo;
use App\Exports\TodosExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TodoController extends Controller
{
    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = Todo::create($request->validated());

        return response()->json([
            'message' => 'Todo created successfully',
            'data' => $todo
        ], 201);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $filters = [
            'title' => $request->get('title'),
            'assignee' => $request->get('assignee'),
            'due_date_from' => $request->get('due_date_from'),
            'due_date_to' => $request->get('due_date_to'),
            'time_tracked_min' => $request->get('time_tracked_min'),
            'time_tracked_max' => $request->get('time_tracked_max'),
            'status' => $request->get('status'),
            'priority' => $request->get('priority'),
        ];

        return Excel::download(new TodosExport($request), 'todos.xlsx');
    }
}
