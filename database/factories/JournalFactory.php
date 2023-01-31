<?php

namespace Database\Factories;

use App\Models\JournalCategory;
use App\Models\JournalGroup;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Journal>
 */
class JournalFactory extends Factory
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
            'state' => 'new',
            'sort' => $this->faker->numberBetween(0, 1500),
            'slug' => $this->faker->slug(6),
            'profile_id' => Profile::factory(),
            'group_id' => JournalGroup::factory(),
            'category_id' => JournalCategory::factory(),
        ];
    }
}
