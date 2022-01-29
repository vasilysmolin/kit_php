<?php

namespace Database\Factories;

use App\Models\CatalogAdCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatalogAdCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatalogAdCategory::class;

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
