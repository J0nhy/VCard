<?php

namespace App\Http\Requests;

use App\Services\Base64Services;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVcardRequest extends FormRequest
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
            'phone_number' => 'required|numeric',
            'name' => 'required|string|max:255', 
            'email' => 'required|email',
            //'confirmation_code' => 'required|string',
            'photo_url' => 'nullable|string',
            'balance' => 'required|numeric',
            'max_debit' => 'required|numeric',
        ];
    }

    /*public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $base64ImagePhoto = $this->base64ImagePhoto ?? null;
            if ($base64ImagePhoto) {
                $base64Service = new Base64Services();
                $mimeType = $base64Service->mimeType($base64ImagePhoto);
                if (!in_array($mimeType, ['image/png', 'image/jpg', 'image/jpeg'])) {
                    $validator->errors()->add('base64ImagePhoto', 'File type not supported (only supports "png" and "jpeg" images).');
                }
            }
        });
    }*/
}
