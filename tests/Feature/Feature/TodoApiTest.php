<?php

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can create a todo with valid data', function () {
    $todoData = [
        'title' => 'Test a new feature',
        'assignee' => 'Alfin',
        'due_date' => now()->addDay()->format('Y-m-d'),
        'priority' => 'high',
    ];

    $this->postJson('/api/todos', $todoData)
        ->assertStatus(201)
        ->assertJsonFragment(['title' => 'Test a new feature']);

    $this->assertDatabaseHas('todos', ['title' => 'Test a new feature', 'assignee' => 'Alfin']);
});

test('it returns validation errors for invalid data', function () {
    $todoData = [
        'title' => '',
        'due_date' => now()->subDay()->format('Y-m-d'),
        'priority' => 'critical',
    ];

    $this->postJson('/api/todos', $todoData)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'due_date', 'priority']);
});

test('it can export todos with filters', function () {
    Todo::factory()->create(['status' => 'completed', 'assignee' => 'Budi']);
    Todo::factory()->create(['status' => 'pending', 'assignee' => 'Ani']);

    $this->get('/api/todos/export?status=completed&assignee=Budi')
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

// DIUBAH: Menyesuaikan dengan struktur response {"status_summary": {...}}
test('it returns chart data by status', function () {
    Todo::factory()->create(['status' => 'pending']);
    Todo::factory()->create(['status' => 'pending']);
    Todo::factory()->create(['status' => 'completed']);

    $this->getJson('/api/chart?type=status')
        ->assertStatus(200)
        ->assertJson([
            'status_summary' => [
                'pending' => 2,
                'completed' => 1,
            ]
        ]);
});

// DIUBAH: Menyesuaikan dengan struktur response {"priority_summary": {...}}
test('it returns chart data by priority', function () {
    Todo::factory()->create(['priority' => 'high']);
    Todo::factory()->create(['priority' => 'low']);
    Todo::factory()->create(['priority' => 'low']);

    $this->getJson('/api/chart?type=priority')
        ->assertStatus(200)
        ->assertJson([
            'priority_summary' => [
                'high' => 1,
                'low' => 2,
            ]
        ]);
});

// DIUBAH: Menyesuaikan dengan struktur response {"assignee_summary": {"Charlie": {...}}}
test('it returns chart data by assignee', function () {
    Todo::factory()->create([
        'assignee' => 'Charlie',
        'status' => 'completed',
        'time_tracked' => 60
    ]);
    Todo::factory()->create([
        'assignee' => 'Charlie',
        'status' => 'pending',
        'time_tracked' => 10
    ]);

    $this->getJson('/api/chart?type=assignee')
        ->assertStatus(200)
        ->assertJsonStructure([
            'assignee_summary' => [
                'Charlie' => [
                    'total_todos',
                    'total_pending_todos',
                    'timetracked_completed_todos',
                ]
            ]
        ])
        ->assertJsonPath('assignee_summary.Charlie.total_todos', 2)
        ->assertJsonPath('assignee_summary.Charlie.total_pending_todos', 1)
        ->assertJsonPath('assignee_summary.Charlie.timetracked_completed_todos', 60);
});
