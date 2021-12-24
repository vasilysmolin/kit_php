<?php

namespace Database\Factories;

use App\Models\JobsResume;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobsResumeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobsResume::class;

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
            'price' => $this->faker->numberBetween(0, 1500),
            'address' => $this->faker->streetAddress(),
        ];
    }
}
