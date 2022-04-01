<?php

namespace App\Http\Requests\Job;

use App\Http\Requests\Helper\Reflector;
use App\Objects\States\States;
use Illuminate\Foundation\Http\FormRequest;

class ResumeIndexRequest extends FormRequest
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

//        $relations = (new Reflector(JobsVacancy::class))->reflector();
        $states = (new States())->keys();
        return [
            'expand' => 'nullable|ends_with:profile.user,profile',
            'status' => "nullable|ends_with:null,{$states}",
            'from' => 'nullable|ends_with:null,cabinet',
            'category_id' => [
                'exists:jobs_vacancy_categories,id',
                'integer',
                'nullable',
                'max:255',
            ],
        ];
    }
}
