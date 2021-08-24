<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetEmailRequest extends FormRequest
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
            'email' => 'required|unique:get_emails,email|email:rfc,dns',
        ];
    }
    public function messages()
    {
        return [
            'email.email' => 'Ой, а вы ошиблись — это не адрес почты.',
            'email.unique' => 'Ой, а этот адрес почты уже кто-то оставил.',
            'email.required' => 'Ой, а вы ошиблись — это не адрес почты.',
        ];
    }
}
