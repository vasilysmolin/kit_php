<?php

namespace Database\Factories;

use App\Models\CategoryRestaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryRestaurantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CategoryRestaurant::class;

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
