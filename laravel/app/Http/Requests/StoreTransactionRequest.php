<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Vcard;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class StoreTransactionRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        $user = Auth::id();
        $user = Vcard::find($user);
        $balance = $user->balance;

        return [

            'type' => 'required|in:C,D',
            'payment_type' => 'required|in:VCARD,MBWAY,PAYPAL,IBAN,MB,VISA',
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'payment_reference' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $paymentType = $request->input('payment_type');

                    switch ($paymentType) {
                        case 'VCARD':
                            // Validar para VCARD - 9 dígitos
                            $rules = ['required', 'regex:/^\d{9}$/', 'exists:vcards,phone_number', 'different:' . $user->phone_number];
                            break;
                        case 'MBWAY':
                            // Validar para MBWAY - qualquer número de telemóvel PT
                            $rules = ['required', 'regex:/^(9[1236]\d{7})$/'];
                            break;
                        case 'PAYPAL':
                            // Validar para PAYPAL - qualquer email válido
                            $rules = ['required', 'email'];
                            break;
                        case 'IBAN':
                            // Validar para IBAN - 2 letras seguidas de 23 dígitos
                            $rules = ['required', 'regex:/^[A-Za-z]{2}\d{23}$/'];
                            break;
                        case 'MB':
                            // Validar para MB - 5 dígitos, hífen, 9 dígitos
                            $rules = ['required', 'regex:/^\d{5}-\d{9}$/'];
                            break;
                        case 'VISA':
                            // Validar para VISA - 16 dígitos, começando com 4
                            $rules = ['required', 'regex:/^4\d{15}$/'];
                            break;
                        default:
                            // Se não é nenhuma das opções, rejeitar
                            $rules = ['not_in:' . $paymentType];
                            break;
                    }

                    $validator = validator([$attribute => $value], [$attribute => $rules]);

                    if ($validator->fails()) {
                        $fail($validator->errors()->first($attribute));
                    }
                },
            ],

            'value' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . $balance,
                function ($attribute, $value, $fail) use ($balance) {
                    // Check if the value is smaller than max_debit
                    if ($value > $balance) {
                        $fail("The $attribute must be smaller than max_debit ($balance).");
                    }
                },
            ],

        ];
    }
}
