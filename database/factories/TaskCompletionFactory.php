<?php

namespace Database\Factories;

use App\Models\Coloc;
use App\Models\Roommate;
use App\Models\Task;
use App\Models\TaskCompletion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskCompletion>
 */
class TaskCompletionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coloc_id' => Coloc::factory(),
            'task_id' => Task::factory(),
            'assigned_roommate_id' => Roommate::factory(),
            'actual_roommate_id' => null,
            'status' => fake()->randomElement(['done', 'not_done', 'done_by_other']),
            'week' => (int) now()->format('W'),
            'year' => now()->year,
        ];
    }
}
