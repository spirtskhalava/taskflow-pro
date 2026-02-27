<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'  => Project::factory(),
            'reporter_id' => User::factory(),
            'assignee_id' => null,
            'title'       => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status'      => fake()->randomElement(['todo', 'in_progress', 'in_review', 'done']),
            'priority'    => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'position'    => fake()->numberBetween(1, 100),
            'due_date'    => fake()->optional()->dateTimeBetween('now', '+30 days'),
        ];
    }
}
