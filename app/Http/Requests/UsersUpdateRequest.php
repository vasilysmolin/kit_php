<?php

namespace App\Http\Requests;

use App\Http\Requests\Helper\Reflector;
use App\Objects\States\States;
use Illuminate\Foundation\Http\FormRequest;

class UsersUpdateRequest extends FormRequest
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
            'status' => "nullable|ends_with:{$states}",
            'name' => 'string|min:1|max:255',
            'max_price' => 'nullable|integer|max:9999999',
            'min_price' => 'integer|max:9999999',
            'description' => 'nullable|string|min:1|max:1000',
            'duties' => 'nullable|string|min:1|max:1000',
            'demands' => 'nullable|string|min:1|max:1000',
            'additionally' => 'nullable|string|min:1|max:1000',
            'education' => 'string|min:1|max:255',
            'experience' => 'string|min:1|max:255',
            'schedule' => 'string|min:1|max:255',
            'salary_type' => 'nullable|string|min:1|max:255',
            'latitude' => 'nullable|numeric|between:0,9999.99',
            'longitude' => 'nullable|numeric|between:0,9999.99',
            'work_experience' => 'nullable|boolean',
            'title' => 'nullable|string|min:1|max:255',
            'address' => 'nullable|string|min:1|max:255',
            'phone' => 'nullable|string|min:1|max:255',
        ];
    }
}
