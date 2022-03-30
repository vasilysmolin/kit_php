<?php

namespace App\Http\Requests\Job;

use App\Http\Requests\Helper\Reflector;
use App\Models\JobsVacancy;
use Illuminate\Foundation\Http\FormRequest;

class VacancyIndexRequest extends FormRequest
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

        $reflector = (new Reflector(JobsVacancy::class));
        dd($reflector);
        return [
            'name' => 'string|min:1|max:255',
            'min_price' => 'integer|max:9999999',
            'category_id' => [
                'exists:jobs_vacancy_categories,id',
                'integer',
                'nullable',
                'max:255',
            ],
        ];
    }
}
