<?php

namespace Database\Factories;

use App\Models\Coloc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coloc>
 */
class ColocFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
        ];
    }
}
