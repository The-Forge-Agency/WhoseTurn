<?php

namespace Database\Factories;

use App\Models\Coloc;
use App\Models\Roommate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Roommate>
 */
class RoommateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coloc_id' => Coloc::factory(),
            'first_name' => fake()->firstName(),
            'avatar_slug' => fake()->randomElement(Roommate::AVATARS),
            'order' => 0,
        ];
    }
}
