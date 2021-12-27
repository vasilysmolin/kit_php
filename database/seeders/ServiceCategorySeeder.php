<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ServiceCategory::firstOrCreate([
            'alias' => 'repair-installation-equipment',
        ])->update([
            'name' => 'Ремонт и установка техники',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'transport-couriers',
        ])->update([
            'name' => 'Перевозки и курьеры',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'repair-construction',
        ])->update([
            'name' => 'Ремонт и строительство',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'household-cleaning',
        ])->update([
            'name' => 'Уборка и помощь по хозяйству',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'tutors-training',
        ])->update([
            'name' => 'Репетиторы и обучение',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'trainers',
        ])->update([
            'name' => 'Тренеры',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'beauty',
        ])->update([
            'name' => 'Красота',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'doctors-health',
        ])->update([
            'name' => 'Доктора (либо Здоровье)',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'computer-help',
        ])->update([
            'name' => 'Компьютерная помощь и IT',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'designers',
        ])->update([
            'name' => 'Дизайнеры',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'photo-video-services',
        ])->update([
            'name' => 'Фото, аудио, видео услуги',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'creativity-hobbies',
        ])->update([
            'name' => 'Творчество, хобби и рукоделие',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'organization-events',
        ])->update([
            'name' => 'Организация мероприятий',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'artists',
        ])->update([
            'name' => 'Артисты',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'security-detectives',
        ])->update([
            'name' => 'Охрана и детективы',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'services-animals',
        ])->update([
            'name' => 'Услуги для животных',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'virtual-assistant',
        ])->update([
            'name' => 'Виртуальный помощник',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'rent',
        ])->update([
            'name' => 'Аренда',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'other',
        ])->update([
            'name' => 'Разное',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'lawyers-advocates',
        ])->update([
            'name' => 'Юристы и адвокаты',
        ]);

        ServiceCategory::firstOrCreate([
            'alias' => 'accountants-financiers',
        ])->update([
            'name' => 'Бухгалтера и финансисты',
        ]);



    }
}
