<?php

namespace App\Http\Requests;

use App\Http\Requests\Helper\Reflector;
use App\Objects\States\States;
use Illuminate\Foundation\Http\FormRequest;

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

//        $relations = (new Reflector(JobsVacancy::class))->reflector();
        $states = (new States())->keys();
        return [
            'state' => "nullable|ends_with:{$states}",
            'name' => 'nullable|string|min:1|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|min:1|max:255',
            'inn' => 'nullable|string|min:1|max:255',
        ];
    }
}
