<?php

namespace Database\Seeders;

use App\Models\CategoryFood;
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

        $user = User::factory(5)
            ->has(Restaurant::factory(5)
                ->has(RestaurantFood::factory(5)


                )
            )
            ->create();
        dd($user);
    }
}
