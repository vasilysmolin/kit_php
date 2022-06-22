<?php

namespace Database\Seeders;

use App\Models\Timezone;
use Illuminate\Database\Seeder;

class TimeZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
           Timezone::firstOrCreate([
               'regular' => '-12',

           ])->update([
                'name' => '(GMT -12:00) Эниветок, Кваджалейн',
                'regular' => '-12',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '-1200',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-11',

           ])->update([
                'name' => '(GMT -11:00) Остров Мидуэй, Самоа',
                'regular' => '-11',
                'summer' => null,
                'winter' => null,
                'created_at' => null,

                'regularFormat' => '-1200',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-10',

           ])->update([
                'name' => '(GMT -10:00) Гавайи',
                'regular' => '-10',
                'summer' => null,
                'winter' => null,
                'created_at' => null,

                'regularFormat' => '-1000',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-9',

           ])->update([
                'name' => '(GMT -9:00) Аляска',
                'regular' => '-9',
                'summer' => null,
                'winter' => null,
                'created_at' => null,

                'regularFormat' => '-0900',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-8',

           ])->update([
                'name' => '(GMT -8:00) Тихоокеанское время (США и Канада)',
                'regular' => '-8',
                'summer' => null,
                'winter' => null,
                'created_at' => null,

                'regularFormat' => '-0800',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-7',

           ])->update([
                'name' => '(GMT -7:00) Горное время (США и Канада)',
                'regular' => '-7',
                'summer' => null,
                'winter' => null,
                'created_at' => null,

                'regularFormat' => '-0700',
                ]);

            Timezone::firstOrCreate([
                'regular' => '-6',

            ])->update([
                'name' => '(GMT -6:00) Центральное время (США и Канада), Мехико',
                'regular' => '-6',
                'summer' => null,
                'winter' => null,
                'created_at' => null,

                'regularFormat' => '-0600',
            ]);

           Timezone::firstOrCreate([
               'regular' => '-5',

           ])->update([
                'name' => '(GMT -5:00) Восточное время (США и Канада), Богота, Лима',
                'regular' => '-5',
                'summer' => null,
                'winter' => null,
                'created_at' => null,

                'regularFormat' => '-0500',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-4',

           ])->update([
                'name' => '(GMT -4:00) Атлантическое время (Канада), Каракас, Ла-Пас',
                'regular' => '-4',
                'summer' => null,
                'winter' => null,
                'created_at' => null,

                'regularFormat' => '-0400',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-3',

           ])->update([
                'name' => '(GMT -3:00) Бразилия, Буэнос-Айрес, Джорджтаун',
                'regular' => '-3',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '-0300',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-2',

           ])->update([
                'name' => '(GMT -2:00) Срединно-Атлантического',
                'regular' => '-2',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '-0200',
                ]);


           Timezone::firstOrCreate([
               'regular' => '-1',

           ])->update([
                'name' => '(GMT -1:00 час) Азорские острова, острова Зеленого Мыса',
                'regular' => '-1',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '-0100',
                ]);


           Timezone::firstOrCreate([
               'regular' => '0',

           ])->update([
                'name' => '(GMT) Время Западной Европе, Лондон, Лиссабон, Касабланка',
                'regular' => '0',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0000',
                ]);


           Timezone::firstOrCreate([
               'regular' => '1',

           ])->update([
                'name' => '(GMT +1:00 час) Брюссель, Копенгаген, Мадрид, Париж',
                'regular' => '1',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0100',
                ]);


           Timezone::firstOrCreate([
               'regular' => '2',

           ])->update([
                'name' => '(GMT +2:00) Киев, Калининград, Южная Африка',
                'regular' => '2',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0200',
                ]);


           Timezone::firstOrCreate([
               'regular' => '3',

           ])->update([
                'name' => '(GMT +3:00) Москва, Санкт-Петербург, Багдад, Эр-Рияд,',
                'regular' => '3',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0300',
                ]);


           Timezone::firstOrCreate([
               'regular' => '4',

           ])->update([
                'name' => '(GMT +4:00) Самара, Баку, Тбилиси',
                'regular' => '4',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0400',
                ]);


           Timezone::firstOrCreate([
               'regular' => '5',

           ])->update([
                'name' => '(GMT +5:00) Екатеринбург, Исламабад, Карачи, Ташкент',
                'regular' => '5',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0500',
                ]);


           Timezone::firstOrCreate([
               'regular' => '6',

           ])->update([
                'name' => '(GMT +6:00) Алматы, Дакке, Коломбо',
                'regular' => '6',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0600',
                ]);


           Timezone::firstOrCreate([
               'regular' => '7',

           ])->update([
                'name' => '(GMT +7:00) Бангкок, Ханой, Джакарта',
                'regular' => '7',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0700',
                ]);


           Timezone::firstOrCreate([
               'regular' => '8',

           ])->update([
                'name' => '(GMT +8:00) Пекин, Перт, Сингапур, Гонконг',
                'regular' => '8',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0800',
                ]);


           Timezone::firstOrCreate([
               'regular' => '9',

           ])->update([
                'name' => '(GMT +9:00) Якутск, Токио, Сеул, Осака, Саппоро ',
                'regular' => '9',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '0900',
                ]);


           Timezone::firstOrCreate([
               'regular' => '10',

           ])->update([
                'name' => '(GMT +10:00) Владивосток, Восточная Австралия, Гуам',
                'regular' => '10',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '1000',
                ]);


           Timezone::firstOrCreate([
               'regular' => '11',

           ])->update([
                'name' => '(GMT +11:00) Магадан, Соломоновы острова, Новая Каледония',
                'regular' => '11',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '1100',
                ]);


           Timezone::firstOrCreate([
               'regular' => '12',

           ])->update([
                'name' => '(GMT +12:00) Камчатка, Окленд, Веллингтон, Фиджи',
                'regular' => '12',
                'summer' => null,
                'winter' => null,
                'created_at' => null,
                'regularFormat' => '1200',
                ]);
    }
}
