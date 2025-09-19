<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use App\Exports\TodosExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TodoController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = Todo::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->get('title') . '%');
        }

        if ($request->filled('assignee')) {
            $query->where('assignee', 'like', '%' . $request->get('assignee') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->get('due_date_from'));
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->get('due_date_to'));
        }

        if ($request->filled('time_tracked_min')) {
            $query->where('time_tracked', '>=', $request->get('time_tracked_min'));
        }

        if ($request->filled('time_tracked_max')) {
            $query->where('time_tracked', '<=', $request->get('time_tracked_max'));
        }

        $todos = $query->paginate(10);

        return response()->json([
            'message' => 'Todos retrieved successfully',
            'data' => $todos->items(),
            'pagination' => [
                'current_page' => $todos->currentPage(),
                'last_page' => $todos->lastPage(),
                'per_page' => $todos->perPage(),
                'total' => $todos->total(),
                'from' => $todos->firstItem(),
                'to' => $todos->lastItem(),
            ]
        ]);
    }

    public function show(Todo $todo): JsonResponse
    {
        return response()->json([
            'message' => 'Todo retrieved successfully',
            'data' => $todo
        ]);
    }

    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = Todo::create($request->validated());

        return response()->json([
            'message' => 'Todo created successfully',
            'data' => $todo
        ], 201);
    }

    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        $todo->update($request->validated());

        return response()->json([
            'message' => 'Todo updated successfully',
            'data' => $todo->fresh()
        ]);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json([
            'message' => 'Todo deleted successfully'
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {


        return Excel::download(new TodosExport($request), 'todos.xlsx');
    }
}
