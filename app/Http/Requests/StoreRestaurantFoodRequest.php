<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantFoodRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'files' => 'nullable|file:1,20000|image',
            'alias' => [
                'unique:restaurant_foods,alias',
                'required',
                'string',
                'max:255',
            ],
            'category_id' => 'required|integer|max:99999999',

        ];
    }
}