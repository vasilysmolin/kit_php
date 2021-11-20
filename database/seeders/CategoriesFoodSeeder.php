<?php

namespace Database\Seeders;

use App\Models\FoodCategoryDishes;
use App\Models\FoodCategoryRestaurant;
use Illuminate\Database\Seeder;

class CategoriesFoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FoodCategoryDishes::firstOrCreate([
            'alias'=>'meat',
        ])->update([
            'name'=>'Мясо',
        ]);

        FoodCategoryDishes::firstOrCreate([
            'alias'=>'fish',
        ])->update([
            'name'=>'Рыба',
        ]);

        FoodCategoryDishes::firstOrCreate([
            'alias'=>'salads',
        ])->update([
            'name'=>'Салаты',
        ]);

        FoodCategoryDishes::firstOrCreate([
            'alias'=>'beverages',
        ])->update([
            'name'=>'Напитки',
        ]);

        FoodCategoryDishes::firstOrCreate([
            'alias'=>'cooking',
        ])->update([
            'name'=>'Кулинария',
        ]);

        FoodCategoryDishes::firstOrCreate([
            'alias'=>'bread',
        ])->update([
            'name'=>'Хлеб',
        ]);

        FoodCategoryDishes::firstOrCreate([
            'alias'=>'stock',
        ])->update([
            'name'=>'Акция',
        ]);
    }
}
