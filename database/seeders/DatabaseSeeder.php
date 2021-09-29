<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use App\Models\Restaurant;
use App\Models\RestaurantFood;
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
            ->has(Restaurant::factory(50)
                ->has(RestaurantFood::factory(10)
                )
            )
            ->create();

        Country::factory(1)
            ->has(Region::factory(5)
                ->has(City::factory(10)
                )
            )
            ->create();
    }
}
