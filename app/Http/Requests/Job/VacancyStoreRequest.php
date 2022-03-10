<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class VacancyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'max_price' => 'required|integer|max:9999999',
            'min_price' => 'required|integer|max:9999999',
            'description' => 'string|min:1|max:1000',
            'duties' => 'string|min:1|max:1000',
            'demands' => 'string|min:1|max:1000',
            'additionally' => 'string|min:1|max:1000',
            'education' => 'string|min:1|max:255',
            'experience' => 'string|min:1|max:255',
            'schedule' => 'string|min:1|max:255',
            'salary_type' => 'string|min:1|max:255',
            'latitude' => 'nullable|numeric|between:0,9999.99',
            'longitude' => 'nullable|numeric|between:0,9999.99',
            'work_experience' => 'nullable|boolean',
            'title' => 'nullable|string|min:1|max:255',
            'address' => 'nullable|string|min:1|max:255',
            'phone' => 'nullable|string|min:1|max:255',
//            'alias' => [
//                'unique:jobs_vacancies,alias',
////                'required',
//                'string',
//                'max:255',
//            ],
            'city_id' => [
                'exists:cities,id',
                'nullable',
                'integer',
                'max:255',
            ],
            'category_id' => [
                'exists:jobs_vacancy_categories,id',
                'nullable',
                'integer',
                'max:255',
            ],
        ];
    }
}
