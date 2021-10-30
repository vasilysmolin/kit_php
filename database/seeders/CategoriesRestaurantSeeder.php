<?php

namespace Database\Seeders;

use App\Models\CategoryRestaurant;
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
        CategoryRestaurant::firstOrCreate([
            'alias'=>'sushi',
        ])->update([
            'name'=>'Суши',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'burgers',
        ])->update([
            'name'=>'Бургеры',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'pizza',
        ])->update([
            'name'=>'Пицца',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'fast-food',
        ])->update([
            'name'=>'Фастфуд',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'caucasian',
        ])->update([
            'name'=>'Кавказская',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'asian',
        ])->update([
            'name'=>'Азиатская',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'breakfast',
        ])->update([
            'name'=>'Завтрак',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'dinner',
        ])->update([
            'name'=>'Обед',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'baby',
        ])->update([
            'name'=>'Детское',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'desserts',
        ])->update([
            'name'=>'Десерты',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'on-coals',
        ])->update([
            'name'=>'На углях',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'bakery',
        ])->update([
            'name'=>'Выпечка',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'shawarma',
        ])->update([
            'name'=>'Шаурма',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'healthy',
        ])->update([
            'name'=>'Здоровая',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'seafood',
        ])->update([
            'name'=>'Морепродукты',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'russian',
        ])->update([
            'name'=>'Русская',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'italian',
        ])->update([
            'name'=>'Итальянская',
        ]);

        CategoryRestaurant::firstOrCreate([
            'alias'=>'american',
        ])->update([
            'name'=>'Американская',
        ]);
    }
}
