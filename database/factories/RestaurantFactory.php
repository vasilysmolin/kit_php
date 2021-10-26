<?php

namespace Database\Factories;

use App\Models\CategoryFood;
use App\Models\CategoryRestaurant;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Restaurant::class;

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
            'user_id' => User::factory(),
            'category_id' => CategoryRestaurant::factory(),
            'min_delivery_price' => $this->faker->numberBetween(0,1500),
            'address' => $this->faker->streetAddress(),
            'house' => $this->faker->streetAddress(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
        ];
    }
}
