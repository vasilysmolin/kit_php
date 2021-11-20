<?php

namespace Database\Factories;

use App\Models\FoodCategoryDishes;
use App\Models\FoodRestaurantDishes;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFoodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FoodRestaurantDishes::class;

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
            'price' => $this->faker->numberBetween(100,1000),
            'salePrice' => $this->faker->numberBetween(1,90),
            'description' => $this->faker->text(),
            'active' => 1,
            'quantity' => $this->faker->numberBetween(0,1000),
        ];
    }
}
