<?php

namespace App\Http\Requests;

use App\Http\Requests\Helper\Reflector;
use App\Objects\States\States;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

        $states = (new States())->keys();
        return [
            'state' => [
                Rule::in($states),
            ],
            'name' => 'string|min:1|max:255',
            'email' => 'email',
            'phone' => 'string|min:10|max:10',
            'inn' => 'string|min:1|max:255',
            'city_id' => [
                'exists:cities,id',
                'nullable',
                'integer',
                'max:999999',
            ],
        ];
    }
}
