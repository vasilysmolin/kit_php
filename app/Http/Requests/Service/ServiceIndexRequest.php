<?php

namespace App\Http\Requests\Service;

use App\Http\Requests\Helper\Reflector;
use App\Objects\States\States;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceIndexRequest extends FormRequest
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
                Rule::in(['cabinet']),
            ],
            'category_id' => [
                'exists:services,id',
                'integer',
                'max:255',
            ],
        ];
    }
}
