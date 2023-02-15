<?php

namespace App\Http\Requests\Realty;

use Illuminate\Foundation\Http\FormRequest;

class HouseUpdateRequest extends FormRequest
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
            'name' => 'string|min:1|max:255',
            'name_agent' => 'string|min:1|max:2000',
            'description' => 'string|min:1|max:2000',
            'city_id' => [
                'exists:cities,id',
                'integer',
                'max:9999999999',
            ],
        ];
    }
}
