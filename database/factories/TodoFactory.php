<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assignees = ['Alfin', 'Budi', 'Chandra', 'Dewi', 'Eka', 'Fahmi', 'Gilang'];
        $statuses = ['pending', 'open', 'in_progress', 'completed'];
        $priorities = ['low', 'medium', 'high'];
        $verbs = ['Develop', 'Test', 'Review', 'Deploy', 'Fix', 'Refactor'];
        $nouns = ['API', 'Frontend', 'Database Schema', 'Auth Module', 'Export Feature', 'UI Components'];

        return [
            'title' => fake()->randomElement($verbs) . ' ' . fake()->randomElement($nouns),
            'assignee' => fake()->randomElement($assignees),
            'due_date' => fake()->dateTimeBetween('-1 month', '+2 months'),
            'time_tracked' => fake()->numberBetween(15, 480),
            'status' => fake()->randomElement($statuses),
            'priority' => fake()->randomElement($priorities),
        ];
    }
}
