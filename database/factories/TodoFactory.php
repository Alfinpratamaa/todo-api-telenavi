<?php

// database/factories/TodoFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'assignee' => $this->faker->name,
            'due_date' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'time_tracked' => $this->faker->numberBetween(0, 480),
            'status' => $this->faker->randomElement(['pending', 'open', 'in_progress', 'completed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
        ];
    }
}
