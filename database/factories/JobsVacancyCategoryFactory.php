<?php

namespace Database\Factories;

use App\Models\FoodCategoryRestaurant;
use App\Models\JobsResumeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobsVacancyCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobsResumeCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'alias' => $this->faker->slug(6),
            'active' => 1,
        ];
    }
}
