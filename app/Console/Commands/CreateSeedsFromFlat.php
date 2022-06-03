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
        $flatCats = CatalogAdCategory::whereIn('id', [12, 383])->get();
        foreach ($flatCats as $flatCat) {
            $filters = $flatCat->filters;
            if ($flatCat->getKey() === 383) {
                $slug = '-rent';
            }
            if ($flatCat->getKey() === 12) {
                $slug = '-bye';
            }
            if ($filters->isEmpty()) {
                $filter = $flatCat->filters()->create([
                    'name' => 'Колличество комнат',
                    'type' => 'select',
                    'alias' => Str::slug('Колличество комнат') . $slug,
                    'sort' => 1,
                    'active' => 1,
                ]);
                $filter->parameters()->create([
                    'value' => '1 комнатная',
                    'sort' => 1,
                ]);
                $filter->parameters()->create([
                    'value' => '2 комнатная',
                    'sort' => 2,
                ]);
                $filter->parameters()->create([
                    'value' => '3 комнатная',
                    'sort' => 3,
                ]);
                $filter->parameters()->create([
                    'value' => '4 комнатная',
                    'sort' => 4,
                ]);
                $filter->parameters()->create([
                    'value' => '5 комнатная',
                    'sort' => 5,
                ]);


                $filter = $flatCat->filters()->create([
                    'name' => 'Всего этажей',
                    'type' => 'range',
                    'alias' => Str::slug('Всего этажей')  . $slug,
                    'sort' => 2,
                    'active' => 1,
                ]);

                for ($i = 1; $i <= 100; $i++) {
                    $filter->parameters()->create([
                        'value' => $i,
                        'sort' => $i,
                    ]);
                }

                $filter = $flatCat->filters()->create([
                    'name' => 'Этаж',
                    'type' => 'range',
                    'alias' => Str::slug('Этаж')  . $slug,
                    'sort' => 3,
                    'active' => 1,
                ]);

                for ($i = 1; $i <= 100; $i++) {
                    $filter->parameters()->create([
                        'value' => $i,
                        'sort' => $i,
                    ]);
                }

                $filter = $flatCat->filters()->create([
                    'name' => 'Дом',
                    'type' => 'select',
                    'alias' => Str::slug('Дом')  . $slug,
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
                    'type' => 'select',
                    'alias' => Str::slug('Продавец')  . $slug,
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
                    'type' => 'select',
                    'alias' => Str::slug('Новизна')  . $slug,
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
                    'type' => 'range',
                    'alias' => Str::slug('Общая площадь')  . $slug,
                    'sort' => 7,
                    'active' => 1,
                ]);

                for ($i = 1; $i <= 100; $i++) {
                    $filter->parameters()->create([
                        'value' => $i,
                        'sort' => $i,
                    ]);
                }

                $filter = $flatCat->filters()->create([
                    'name' => 'Жилая площадь',
                    'type' => 'range',
                    'alias' => Str::slug('Жилая площадь')  . $slug,
                    'sort' => 8,
                    'active' => 1,
                ]);

                for ($i = 1; $i <= 100; $i++) {
                    $filter->parameters()->create([
                        'value' => $i,
                        'sort' => $i,
                    ]);
                }

                $filter = $flatCat->filters()->create([
                    'name' => 'Площадь кухни',
                    'type' => 'range',
                    'alias' => Str::slug('Площадь кухни')  . $slug,
                    'sort' => 9,
                    'active' => 1,
                ]);

                for ($i = 1; $i <= 100; $i++) {
                    $filter->parameters()->create([
                        'value' => $i,
                        'sort' => $i,
                    ]);
                }

                $filter = $flatCat->filters()->create([
                    'name' => 'Удобства',
                    'type' => 'checkbox',
                    'alias' => Str::slug('Удобства')  . $slug,
                    'sort' => 10,
                    'active' => 1,
                ]);


                $filter->parameters()->create([
                    'value' => 'Телефон',
                    'sort' => 1,
                ]);

                $filter->parameters()->create([
                    'value' => 'Интернет',
                    'sort' => 2,
                ]);

                $filter->parameters()->create([
                    'value' => 'Парковка',
                    'sort' => 3,
                ]);

                $filter->parameters()->create([
                    'value' => 'Два лифта',
                    'sort' => 4,
                ]);

                $filter->parameters()->create([
                    'value' => 'Консьерж',
                    'sort' => 4,
                ]);

                $filter->parameters()->create([
                    'value' => 'Балкон',
                    'sort' => 4,
                ]);
            }
        }

        return 0;
    }
}
