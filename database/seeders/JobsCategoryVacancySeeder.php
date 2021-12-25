<?php

namespace Database\Seeders;

use App\Models\FoodCategoryRestaurant;
use App\Models\JobsVacancyCategory;
use Illuminate\Database\Seeder;

class JobsCategoryVacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'resume',
        ])->update([
            'name' => 'Резюме',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'vacancies',
        ])->update([
            'name' => 'Вакансии',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'carbusiness-carservice',
        ])->update([
            'name' => 'Автобизнес Автосервис',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'administrative-staff',
        ])->update([
            'name' => 'Административный персонал',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'banks-investments',
        ])->update([
            'name' => 'Банки Инвестиции',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'security-safety',
        ])->update([
            'name' => 'Охрана Безопасность',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'accounting-audit-finance',
        ])->update([
            'name' => 'Бухгалтерия Аудит Финансы',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'top-management',
        ])->update([
            'name' => 'Топ-менеджмент',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'public-service-nko',
        ])->update([
            'name' => 'Государственная служба НКО',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'extraction-processing-raw-materials',
        ])->update([
            'name' => 'Добыча Переработка сырья',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'home-staff',
        ])->update([
            'name' => 'Домашний персонал',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'procurement',
        ])->update([
            'name' => 'Закупки',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'installation-service',
        ])->update([
            'name' => 'Инсталляция Сервис',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'it-internet-telecom',
        ])->update([
            'name' => 'IT Интернет Телеком',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'design-media-art',
        ])->update([
            'name' => 'Дизайн Медиа Искусство',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'counseling',
        ])->update([
            'name' => 'Консультирование',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'marketing-advertising-pr',
        ])->update([
            'name' => 'Маркетинг Реклама PR',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'medical-pharmaceutical',
        ])->update([
            'name' => 'Медицина Фармацевтика',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'education-science',
        ])->update([
            'name' => 'Образование Наука',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'work-students-internships',
        ])->update([
            'name' => 'Работа для студентов Стажировки',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'sales',
        ])->update([
            'name' => 'Продажи',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'production',
        ])->update([
            'name' => 'Производство',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'working-staff',
        ])->update([
            'name' => 'Рабочий персонал',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'sports-fitness-beauty',
        ])->update([
            'name' => 'Спорт Фитнес Красота',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'insurance',
        ])->update([
            'name' => 'Страхование',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'construction-jkh-operation',
        ])->update([
            'name' => 'Строительство ЖКХ Эксплуатация',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'transport-logistics-warehouse',
        ])->update([
            'name' => 'Транспорт Логистика Склад',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'tourism-hotels',
        ])->update([
            'name' => 'Туризм Гостиницы',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'human-resources',
        ])->update([
            'name' => 'HR Кадры Подбор персонала',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'law',
        ])->update([
            'name' => 'Юриспруденция',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'restaurants-cafes-catering',
        ])->update([
            'name' => 'Рестораны Кафе Общепит',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'real-estate-realtor',
        ])->update([
            'name' => 'Недвижимость Риэлторские услуги',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'work-without-experience',
        ])->update([
            'name' => 'Работа без опыта',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'agroprom',
        ])->update([
            'name' => 'Агропром',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'veterinary',
        ])->update([
            'name' => 'Ветеринария',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'charity-volunteering',
        ])->update([
            'name' => 'Благотворительность',
        ]);
        JobsVacancyCategory::firstOrCreate([
            'alias' => 'other',
        ])->update([
            'name' => 'Другое',
        ]);
    }
}
