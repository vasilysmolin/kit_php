<?php

namespace Database\Seeders;

use App\Models\CategoryFood;
use App\Models\CategoryRestaurant;
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
        CategoryFood::firstOrCreate([
            'alias'=>'meat',
        ])->update([
            'name'=>'Мясо',
        ]);

        CategoryFood::firstOrCreate([
            'alias'=>'fish',
        ])->update([
            'name'=>'Рыба',
        ]);

        CategoryFood::firstOrCreate([
            'alias'=>'salads',
        ])->update([
            'name'=>'Салаты',
        ]);

        CategoryFood::firstOrCreate([
            'alias'=>'beverages',
        ])->update([
            'name'=>'Напитки',
        ]);

        CategoryFood::firstOrCreate([
            'alias'=>'cooking',
        ])->update([
            'name'=>'Кулинария',
        ]);

        CategoryFood::firstOrCreate([
            'alias'=>'bread',
        ])->update([
            'name'=>'Хлеб',
        ]);

        CategoryFood::firstOrCreate([
            'alias'=>'stock',
        ])->update([
            'name'=>'Акция',
        ]);
    }
}
