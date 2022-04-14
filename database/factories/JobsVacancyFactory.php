<?php

namespace Database\Factories;

use App\Models\JobsVacancy;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobsVacancyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobsVacancy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'active' => 1,
            'alias' => $this->faker->slug(6),
            'sort' => $this->faker->numberBetween(0, 1500),
            'profile_id' => Profile::factory(),
            'min_price' => $this->faker->numberBetween(0, 1500),
            'max_price' => $this->faker->numberBetween(0, 1500),
            'address' => $this->faker->streetAddress(),
        ];
    }
}
