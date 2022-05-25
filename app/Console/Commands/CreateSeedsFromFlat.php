<?php

namespace App\Console\Commands;

use App\Models\CatalogAdCategory;
use App\Models\CatalogParameter;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateSeedsFromFlat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-seeds-flat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds command from flat';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $flatCat = CatalogAdCategory::find(12);
        $filters = $flatCat->filters;
        if ($filters->isEmpty()) {
            $filter = $flatCat->filters()->create([
                'name' => 'Колличество комнат',
                'alias' => Str::slug('Колличество комнат'),
                'sort' => 1,
                'active' => 1,
            ]);
            $filter->parameters()->create([
                'value' => '1 комнатные',
                'sort' => 1,
            ]);
            $filter->parameters()->create([
                'value' => '2 комнатные',
                'sort' => 2,
            ]);
            $filter->parameters()->create([
                'value' => '3 комнатные',
                'sort' => 3,
            ]);
            $filter->parameters()->create([
                'value' => '4 комнатные',
                'sort' => 4,
            ]);
            $filter->parameters()->create([
                'value' => '5 комнатные',
                'sort' => 5,
            ]);


            $filter = $flatCat->filters()->create([
                'name' => 'Всего этажей',
                'alias' => Str::slug('Всего этажей'),
                'sort' => 2,
                'active' => 1,
            ]);

            for($i = 1; $i <= 100; $i++) {
                $filter->parameters()->create([
                    'value' => $i,
                    'sort' => $i,
                ]);
            }

            $filter = $flatCat->filters()->create([
                'name' => 'Этаж',
                'alias' => Str::slug('Этаж'),
                'sort' => 3,
                'active' => 1,
            ]);

            for($i = 1; $i <= 100; $i++) {
                $filter->parameters()->create([
                    'value' => $i,
                    'sort' => $i,
                ]);
            }

            $filter = $flatCat->filters()->create([
                'name' => 'Дом',
                'alias' => Str::slug('Дом'),
                'sort' => 4,
                'active' => 1,
            ]);
            $filter->parameters()->create([
                'value' => 'Панельный',
                'sort' => 1,
            ]);
            $filter->parameters()->create([
                'value' => 'Кирпичный',
                'sort' => 2,
            ]);
            $filter->parameters()->create([
                'value' => 'Деревянный',
                'sort' => 3,
            ]);
            $filter->parameters()->create([
                'value' => 'Шлакоблоки',
                'sort' => 4,
            ]);

            $filter = $flatCat->filters()->create([
                'name' => 'Продавец',
                'alias' => Str::slug('Продавец'),
                'sort' => 5,
                'active' => 1,
            ]);
            $filter->parameters()->create([
                'value' => 'Собственник',
                'sort' => 1,
            ]);
            $filter->parameters()->create([
                'value' => 'Посредник',
                'sort' => 2,
            ]);

            $filter = $flatCat->filters()->create([
                'name' => 'Новизна',
                'alias' => Str::slug('Новизна'),
                'sort' => 6,
                'active' => 1,
            ]);
            $filter->parameters()->create([
                'value' => 'Вторичка',
                'sort' => 1,
            ]);
            $filter->parameters()->create([
                'value' => 'Новостройка',
                'sort' => 2,
            ]);

            $filter = $flatCat->filters()->create([
                'name' => 'Общая площадь',
                'alias' => Str::slug('Общая площадь'),
                'sort' => 7,
                'active' => 1,
            ]);

            for($i = 1; $i <= 100; $i++) {
                $filter->parameters()->create([
                    'value' => $i,
                    'sort' => $i,
                ]);
            }

            $filter = $flatCat->filters()->create([
                'name' => 'Жилая площадь',
                'alias' => Str::slug('Жилая площадь'),
                'sort' => 8,
                'active' => 1,
            ]);

            for($i = 1; $i <= 100; $i++) {
                $filter->parameters()->create([
                    'value' => $i,
                    'sort' => $i,
                ]);
            }

            $filter = $flatCat->filters()->create([
                'name' => 'Площадь кухни',
                'alias' => Str::slug('Площадь кухни'),
                'sort' => 9,
                'active' => 1,
            ]);

            for($i = 1; $i <= 100; $i++) {
                $filter->parameters()->create([
                    'value' => $i,
                    'sort' => $i,
                ]);
            }


        }
        return 0;
    }
}
