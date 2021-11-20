<?php

namespace Database\Seeders;

use App\Models\FoodCategoryRestaurant;
use Illuminate\Database\Seeder;

class CategoriesRestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'sushi',
        ])->update([
            'name'=>'Суши',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'burgers',
        ])->update([
            'name'=>'Бургеры',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'pizza',
        ])->update([
            'name'=>'Пицца',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'fast-food',
        ])->update([
            'name'=>'Фастфуд',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'caucasian',
        ])->update([
            'name'=>'Кавказская',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'asian',
        ])->update([
            'name'=>'Азиатская',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'breakfast',
        ])->update([
            'name'=>'Завтрак',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'dinner',
        ])->update([
            'name'=>'Обед',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'baby',
        ])->update([
            'name'=>'Детское',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'desserts',
        ])->update([
            'name'=>'Десерты',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'on-coals',
        ])->update([
            'name'=>'На углях',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'bakery',
        ])->update([
            'name'=>'Выпечка',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'shawarma',
        ])->update([
            'name'=>'Шаурма',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'healthy',
        ])->update([
            'name'=>'Здоровая',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'seafood',
        ])->update([
            'name'=>'Морепродукты',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'russian',
        ])->update([
            'name'=>'Русская',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'italian',
        ])->update([
            'name'=>'Итальянская',
        ]);

        FoodCategoryRestaurant::firstOrCreate([
            'alias'=>'american',
        ])->update([
            'name'=>'Американская',
        ]);
    }
}
