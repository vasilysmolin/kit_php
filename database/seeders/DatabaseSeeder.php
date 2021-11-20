<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Profile;
use App\Models\Region;
use App\Models\FoodRestaurant;
use App\Models\FoodRestaurantDishes;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        User::factory(1)
            ->has(Profile::factory(1)
                ->has(FoodRestaurant::factory(1)
                    ->has(FoodRestaurantDishes::factory(5))))
            ->create();

        Country::factory(1)
            ->has(Region::factory(1)
                ->has(City::factory(2)))
            ->create();
    }
}
