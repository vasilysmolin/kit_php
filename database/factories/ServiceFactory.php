<?php

namespace Database\Factories;

use App\Models\JobsVacancy;
use App\Models\Profile;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

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
            'title' => $this->faker->name(),
            'active' => 1,
            'sort' => $this->faker->numberBetween(0, 1500),
            'alias' => $this->faker->slug(6),
            'profile_id' => Profile::factory(),
            'price' => $this->faker->numberBetween(0, 1500),
            'address' => $this->faker->streetAddress(),
        ];
    }
}
