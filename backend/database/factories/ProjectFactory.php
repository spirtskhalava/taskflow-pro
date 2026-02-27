<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id'    => User::factory(),
            'name'        => fake()->words(3, true),
            'description' => fake()->sentence(),
            'color'       => fake()->hexColor(),
            'deadline'    => fake()->optional()->dateTimeBetween('now', '+6 months'),
            'is_archived' => false,
        ];
    }

    public function archived(): static
    {
        return $this->state(['is_archived' => true]);
    }
}
