<?php

namespace App\Http\Requests\Realty;

use App\Objects\States\States;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RealtyIndexRequest extends FormRequest
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

        $states = (new States())->keys();
        return [
            'expand' => [
                Rule::in(['profile', 'profile.user','profile.person','profile.user,profile.person']),
            ],
            'state' => [
                Rule::in($states),
            ],
            'from' => [
                Rule::in(['cabinet','catalog']),
            ],
            'filter' => 'array',
            'priceFrom' => 'string',
            'priceTo' => 'string',
            'category_id' => [
                'exists:realty_categories,id',
                'integer',
                'max:255',
            ],
            'alias' => [
                'exists:realty_categories,alias',
                'string',
                'max:255',
            ],
            'querySearch' => [
                'max:255',
                'string',
            ],
        ];
    }
}
