<?php

namespace Database\Factories;

use App\Models\FoodDishesCategory;
use App\Models\FoodCategoryRestaurant;
use App\Models\FoodRestaurant;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodRestaurantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FoodRestaurant::class;

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
            'profile_id' => Profile::factory(),
            'min_delivery_price' => $this->faker->numberBetween(0,1500),
            'address' => $this->faker->streetAddress(),
            'house' => $this->faker->streetAddress(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
        ];
    }
}
