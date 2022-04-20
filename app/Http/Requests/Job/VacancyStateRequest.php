<?php

namespace App\Http\Requests\Job;

use App\Objects\States\States;
use Illuminate\Foundation\Http\FormRequest;

class VacancyStateRequest extends FormRequest
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
            'state' => "ends_with:{$states}",

        ];
    }
}
