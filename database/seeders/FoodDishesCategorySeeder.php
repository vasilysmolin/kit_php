<?php

namespace Database\Seeders;

use App\Models\FoodDishesCategory;
use Illuminate\Database\Seeder;

class FoodDishesCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FoodDishesCategory::firstOrCreate([
            'alias' => 'meat',
        ])->update([
            'name' => 'Мясо',
        ]);

        FoodDishesCategory::firstOrCreate([
            'alias' => 'fish',
        ])->update([
            'name' => 'Рыба',
        ]);

        FoodDishesCategory::firstOrCreate([
            'alias' => 'salads',
        ])->update([
            'name' => 'Салаты',
        ]);

        FoodDishesCategory::firstOrCreate([
            'alias' => 'beverages',
        ])->update([
            'name' => 'Напитки',
        ]);

        FoodDishesCategory::firstOrCreate([
            'alias' => 'cooking',
        ])->update([
            'name' => 'Кулинария',
        ]);

        FoodDishesCategory::firstOrCreate([
            'alias' => 'bread',
        ])->update([
            'name' => 'Хлеб',
        ]);

        FoodDishesCategory::firstOrCreate([
            'alias' => 'stock',
        ])->update([
            'name' => 'Акция',
        ]);
    }
}
