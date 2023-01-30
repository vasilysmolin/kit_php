<?php

namespace Database\Factories;

use App\Models\JournalCategory;
use App\Models\JournalGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JournalCategory>
 */
class JournalCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'active' => 1,
            'sort' => $this->faker->numberBetween(0, 1500),
            'slug' => $this->faker->slug(6),
        ];
    }
}
