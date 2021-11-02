<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantRequest extends FormRequest
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
            'name' => 'string|max:255',
//            'files' => 'file:1,20000|image',
            'alias' => [
                'unique:restaurants,alias',
                'string',
                'max:255',
            ],
            'address' => 'array',
            'categoryRestaurantID' => 'array',
            'isDelivery' => 'boolean',
            'isPickup' => 'boolean',

        ];
    }

//    public function messages(): array
//    {
//        return [
//            'required' => 'тест',
//        ];
//    }
}
