<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class VacancyUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|min:1|max:255',
            'max_price' => 'integer|max:9999999',
            'min_price' => 'integer|max:9999999',
            'description' => 'string|min:1|max:1000',
            'duties' => 'string|min:1|max:1000',
            'demands' => 'string|min:1|max:1000',
            'additionally' => 'string|min:1|max:1000',
            'education' => 'string|min:1|max:255',
            'experience' => 'string|min:1|max:255',
            'schedule' => 'string|min:1|max:255',
            'salary_type' => 'string|min:1|max:255',
            'latitude' => 'string|numeric|between:0,9999.99',
            'longitude' => 'string|numeric|between:0,9999.99',
            'work_experience' => 'string|boolean',
            'title' => 'string|min:1|max:255',
            'address' => 'string|min:1|max:255',
            'phone' => 'string|min:1|max:255',
            'alias' => [
                'unique:jobs_vacancies,alias',
                'string',
                'max:255',
            ],
            'city_id' => [
                'exists:cities,id',
                'integer',
                'max:255',
            ],
            'category_id' => [
                'exists:jobs_vacancy_categories,id',
                'integer',
                'max:255',
            ],
        ];
    }
}
