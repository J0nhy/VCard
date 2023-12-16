<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreVcardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:vcards',
            'phone_number' => 'required|unique:vcards|max:9|min:9',
            'password' => 'required|max:50|min:3',
            'confirmation_code' => 'required|max:3|min:3',
            'password_confirmation' => 'required|same:password',
            //make phone_number unique and required

        ];
    }
}
