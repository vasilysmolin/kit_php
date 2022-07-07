<?php

namespace App\Http\Requests\Ad;

use Illuminate\Foundation\Http\FormRequest;

class AdUpdateRequest extends FormRequest
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
            'price' => 'integer|min:1|max:9999999',
            'description' => 'string|min:1|max:2000',
            'city_id' => [
                'exists:cities,id',
                'integer',
                'max:9999999999',
            ],
            'category_id' => [
                'exists:catalog_ad_categories,id',
                'integer',
                'max:99999999999',
            ],
        ];
    }
}
