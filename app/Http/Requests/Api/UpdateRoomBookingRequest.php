<?php

namespace App\Http\Requests\Api;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomBookingRequest extends FormRequest
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
            'room_id' => 'sometimes|exists:rooms,id',
            'check_in_date' => 'sometimes|date',
            'check_out_date' => 'sometimes|date|after:check_in_date',
            'adults_count' => 'sometimes|integer|min:1',
            'children_count' => 'nullable|integer|min:0',
            'infants_count' => 'nullable|integer|min:0',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $roomId = $this->input('room_id');
            $adults = (int) $this->input('adults_count', 0);
            $children = (int) $this->input('children_count', 0);
            $infants = (int) $this->input('infants_count', 0);

            if ($roomId) {
                $room = Room::find($roomId);
                $totalGuests = $adults + $children + $infants;
                if ($room && $totalGuests > $room->capacity) {
                    $validator->errors()->add(
                        'capacity',
                        "Number of guests ($totalGuests) exceeds room capacity ({$room->capacity})."
                    );
                }
            }
        });
    }
}

