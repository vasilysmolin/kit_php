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
//            'files' => 'nullable|file:1,20000|image',
            'price' => 'required|integer|max:9999999',
            'salePrice' => 'integer|max:9999999',
            'popular' => 'boolean',
            'sale' => 'boolean',
            'novetly' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'unique' => 'не уникален',
        ];
    }
}
