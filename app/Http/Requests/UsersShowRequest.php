<?php

namespace App\Http\Requests;

use App\Http\Requests\Helper\Reflector;
use App\Objects\States\States;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsersShowRequest extends FormRequest
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
                Rule::in([
                    'profile',
                    'profile.person',
                    'profile.resume',
                    'profile.resume,profile.vacancy',
                    'profile.vacancy,profile.resume',
                    'profile.ads',
                    'profile.service,profile.ads',
                    'profile.service,profile.ads,profile.vacancy,profile.resume',
                ]),
            ],
            'state' => [
                Rule::in($states),
            ],
            'from' => [
                Rule::in(['cabinet']),
            ],
            'name' => 'string|max:255',
            'phone' => 'string|max:15',
        ];
    }
}
