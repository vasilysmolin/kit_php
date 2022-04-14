<?php

namespace Database\Factories;

use App\Models\CatalogAd;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatalogAdFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatalogAd::class;

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
            'sort' => $this->faker->numberBetween(0, 1500),
            'alias' => $this->faker->slug(6),
            'profile_id' => Profile::factory(),
            'price' => $this->faker->numberBetween(500, 1500),
            'sale_price' => $this->faker->numberBetween(10, 400),
        ];
    }
}
