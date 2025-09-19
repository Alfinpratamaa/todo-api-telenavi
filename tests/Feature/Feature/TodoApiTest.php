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
        'title' => '', // Required field is empty
        'due_date' => now()->subDay()->format('Y-m-d'), // Past date
        'priority' => 'critical', // Invalid enum
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

test('it returns chart data by status', function () {
    Todo::factory()->create(['status' => 'pending']);
    Todo::factory()->create(['status' => 'pending']);
    Todo::factory()->create(['status' => 'completed']);

    $this->getJson('/api/chart?type=status')
        ->assertStatus(200)
        ->assertJson([
            'pending' => 2,
            'completed' => 1,
        ]);
});

test('it returns chart data by priority', function () {
    Todo::factory()->create(['priority' => 'high']);
    Todo::factory()->create(['priority' => 'low']);
    Todo::factory()->create(['priority' => 'low']);

    $this->getJson('/api/chart?type=priority')
        ->assertStatus(200)
        ->assertJson([
            'high' => 1,
            'low' => 2,
        ]);
});

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

    $response = $this->getJson('/api/chart?type=assignee')
        ->assertStatus(200)
        ->assertJsonStructure([
            'type',
            'data' => [
                '*' => [
                    'assignee',
                    'total_todos',
                    'pending_todos',
                    'total_time_tracked_completed'
                ]
            ]
        ]);

    $data = $response->json();
    $charlieData = collect($data['data'])->firstWhere('assignee', 'Charlie');

    expect($charlieData['total_todos'])->toBe(2);
    expect($charlieData['pending_todos'])->toBe(1);
    expect($charlieData['total_time_tracked_completed'])->toBe(60);
});
