<?php

namespace App\Http\Requests\Api;

use App\Trait\ApiValidationHandler;
use Illuminate\Foundation\Http\FormRequest;

class FlightBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    use ApiValidationHandler;

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
            'flight_id' => 'required|exists:flights,id',
            'seat_id' => 'required|exists:flight_seats,id',
        ];
    }
}
