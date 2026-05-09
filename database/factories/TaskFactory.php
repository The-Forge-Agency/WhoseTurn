<?php

namespace Database\Factories;

use App\Models\Coloc;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $task = fake()->randomElement(Coloc::DEFAULT_TASKS);

        return [
            'coloc_id' => Coloc::factory(),
            'name' => $task['name'],
            'icon_slug' => $task['icon_slug'],
            'enabled' => true,
            'order' => 0,
        ];
    }
}
