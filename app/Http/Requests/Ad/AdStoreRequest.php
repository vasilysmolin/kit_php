<?php

namespace App\Http\Requests\Ad;

use Illuminate\Foundation\Http\FormRequest;

class AdStoreRequest extends FormRequest
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
            'price' => 'required|integer|min:1|max:9999999',
            'description' => 'string|min:1|max:2000',
            'city_id' => [
                'exists:cities,id',
                'nullable',
                'integer',
                'max:255',
            ],
            'category_id' => [
                'required',
                'exists:jobs_vacancy_categories,id',
                'nullable',
                'integer',
                'max:255',
            ],
        ];
    }
}
