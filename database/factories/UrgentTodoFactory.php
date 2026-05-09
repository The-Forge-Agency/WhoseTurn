<?php

namespace Database\Factories;

use App\Models\Coloc;
use App\Models\UrgentTodo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UrgentTodo>
 */
class UrgentTodoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coloc_id' => Coloc::factory(),
            'title' => fake()->sentence(4),
            'done' => false,
        ];
    }
}
