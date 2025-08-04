<?php

namespace App\Http\Requests\Api;

use App\Trait\ApiValidationHandler;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class loginRequest extends FormRequest
{
    use ApiValidationHandler;

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
            'email' => 'required|string|email|max:255',
            'password' => ['required','string',Password::defaults(),]
        ];
    }
}
