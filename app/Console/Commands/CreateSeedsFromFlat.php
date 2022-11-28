<?php

namespace App\Console\Commands;

use App\Models\RealtyCategory;
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
        $flatCats = RealtyCategory::whereIn('id', [12, 383])->get();
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

                for ($i = 1; $i <= 100; $i += 1) {
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

                for ($i = 1; $i <= 100; $i += 1) {
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
                $filter->parameters()->create([
                    'value' => 'Монолитный',
                    'sort' => 5,
                ]);
                $filter->parameters()->create([
                    'value' => 'Блочный',
                    'sort' => 6,
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

                for ($i = 1; $i <= 100; $i += 1) {
                    $filter->parameters()->create([
                        'value' => "{$i}м2",
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

                for ($i = 1; $i <= 100; $i += 1) {
                    $filter->parameters()->create([
                        'value' => "{$i}м2",
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

                for ($i = 1; $i <= 100; $i += 1) {
                    $filter->parameters()->create([
                        'value' => "{$i}м2",
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
                $filter->parameters()->create([
                    'value' => 'Мусоропровод',
                    'sort' => 5,
                ]);
            }
            if (empty($flatCat->filters()->where('name', 'Тип комнат')->first())) {
                $filter = $flatCat->filters()->create([
                    'name' => 'Тип комнат',
                    'type' => 'select',
                    'alias' => Str::slug('Тип комнат') . $slug,
                    'sort' => 12,
                    'active' => 1,
                ]);
                $filter->parameters()->create([
                    'value' => 'Изолированные',
                    'sort' => 1,
                ]);

                $filter->parameters()->create([
                    'value' => 'Смежные',
                    'sort' => 2,
                ]);
            }
            if (empty($flatCat->filters()->where('name', 'Вид из окон')->first())) {
                $filter = $flatCat->filters()->create([
                    'name' => 'Вид из окон',
                    'type' => 'select',
                    'alias' => Str::slug('Вид из окон') . $slug,
                    'sort' => 13,
                    'active' => 1,
                ]);
                $filter->parameters()->create([
                    'value' => 'Во двор',
                    'sort' => 1,
                ]);

                $filter->parameters()->create([
                    'value' => 'На улицу',
                    'sort' => 2,
                ]);

                $filter->parameters()->create([
                    'value' => 'На солнечную сторону',
                    'sort' => 3,
                ]);

                $filter->parameters()->create([
                    'value' => 'На 3 стороны',
                    'sort' => 4,
                ]);
            }
            if (empty($flatCat->filters()->where('name', 'Ремонт')->first())) {
                $filter = $flatCat->filters()->create([
                    'name' => 'Ремонт',
                    'type' => 'select',
                    'alias' => Str::slug('Ремонт') . $slug,
                    'sort' => 14,
                    'active' => 1,
                ]);
                $filter->parameters()->create([
                    'value' => 'Требуется',
                    'sort' => 1,
                ]);

                $filter->parameters()->create([
                    'value' => 'Косметический',
                    'sort' => 2,
                ]);

                $filter->parameters()->create([
                    'value' => 'Евро',
                    'sort' => 3,
                ]);

                $filter->parameters()->create([
                    'value' => 'Дизайнерский',
                    'sort' => 4,
                ]);
            }

            if (empty($flatCat->filters()->where('name', 'Элитный')->first())) {
                $filter = $flatCat->filters()->create([
                    'name' => 'Элитный',
                    'type' => 'select',
                    'alias' => Str::slug('Элитный') . $slug,
                    'sort' => 14,
                    'active' => 1,
                ]);
                $filter->parameters()->create([
                    'value' => 'Да',
                    'sort' => 1,
                ]);

                $filter->parameters()->create([
                    'value' => 'Нет',
                    'sort' => 2,
                ]);
            }


        }
//        $items = CatalogAdCategory::whereIn('id', [2])->get();
//        foreach ($items as $item) {
//            $filters = $item->filters;
//            if ($filters->isEmpty()) {
//                $filter = $item->filters()->create([
//                    'name' => 'Продавец',
//                    'type' => 'select',
//                    'alias' => Str::slug('Продавец машин'),
//                    'sort' => 1,
//                    'active' => 1,
//                ]);
//                $filter->parameters()->create([
//                    'value' => 'Собственник',
//                    'sort' => 1,
//                ]);
//                $filter->parameters()->create([
//                    'value' => 'Автосалон',
//                    'sort' => 2,
//                ]);
//                $filter->parameters()->create([
//                    'value' => 'Посредник',
//                    'sort' => 3,
//                ]);
//
//                $filter = $item->filters()->create([
//                    'name' => 'Двигатель',
//                    'type' => 'select',
//                    'alias' => Str::slug('Двигатель'),
//                    'sort' => 2,
//                    'active' => 1,
//                ]);
//                    $filter->parameters()->create([
//                        'value' => 'Дизель',
//                        'sort' => 1,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Гибрид',
//                        'sort' => 2,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Электро',
//                        'sort' => 3,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Газ',
//                        'sort' => 4,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Бензин',
//                        'sort' => 5,
//                    ]);
//
//                $filter = $item->filters()->create([
//                    'name' => 'Состояние',
//                    'type' => 'select',
//                    'alias' => Str::slug('Состояние'),
//                    'sort' => 3,
//                    'active' => 1,
//                ]);
//                    $filter->parameters()->create([
//                        'value' => 'Не битый',
//                        'sort' => 1,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Битый',
//                        'sort' => 2,
//                    ]);
//
//                $filter = $item->filters()->create([
//                    'name' => 'Ремонт',
//                    'type' => 'select',
//                    'alias' => Str::slug('Ремонт'),
//                    'sort' => 4,
//                    'active' => 1,
//                ]);
//                    $filter->parameters()->create([
//                        'value' => 'Требуется',
//                        'sort' => 1,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Не требуется',
//                        'sort' => 2,
//                    ]);
//
//                $filter = $item->filters()->create([
//                    'name' => 'Птс',
//                    'type' => 'select',
//                    'alias' => Str::slug('Птс'),
//                    'sort' => 5,
//                    'active' => 1,
//                ]);
//                    $filter->parameters()->create([
//                        'value' => 'Копия',
//                        'sort' => 1,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Оригинал',
//                        'sort' => 2,
//                    ]);
//
//                $filter = $item->filters()->create([
//                    'name' => 'Дтп',
//                    'type' => 'select',
//                    'alias' => Str::slug('Дтп'),
//                    'sort' => 6,
//                    'active' => 1,
//                ]);
//                    $filter->parameters()->create([
//                        'value' => 'Участвовал',
//                        'sort' => 1,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Не участвовал',
//                        'sort' => 2,
//                    ]);
//
//                $filter = $item->filters()->create([
//                    'name' => 'Кредит',
//                    'type' => 'select',
//                    'alias' => Str::slug('Кредит'),
//                    'sort' => 6,
//                    'active' => 1,
//                ]);
//                    $filter->parameters()->create([
//                        'value' => 'В залоге',
//                        'sort' => 1,
//                    ]);
//                    $filter->parameters()->create([
//                        'value' => 'Не в залоге',
//                        'sort' => 2,
//                    ]);
//            }
//        }

        return 0;
    }
}
