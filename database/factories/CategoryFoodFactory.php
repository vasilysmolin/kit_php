<?php

namespace Database\Factories;

use App\Models\CategoryFood;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFoodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CategoryFood::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'alias' => $this->faker->md5,
            'active' => 1,
        ];
    }
}
