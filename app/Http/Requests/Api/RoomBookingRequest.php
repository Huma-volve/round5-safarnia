<?php

namespace App\Http\Requests\Api;

use App\Models\Room;
use App\Models\RoomAvailability;
use Illuminate\Foundation\Http\FormRequest;

class RoomBookingRequest extends FormRequest
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
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults_count' => 'required|integer|min:1',
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
            $checkIn = $this->input('check_in_date');
            $checkOut = $this->input('check_out_date');

            $room = Room::find($roomId);
            // التحقق من أن عدد الأفراد يتناسب مع سعة الغرفة
            $totalGuests = $adults + $children + $infants;
            if ($totalGuests > $room->capacity) {
                $validator->errors()->add(
                    'capacity',
                    "Number of guests ($totalGuests) is greater than room capacity ({$room->capacity})."
                );
            }
            //  التحقق من توفر الغرفة ضمن التواريخ المطلوبة
            $availableSlot = RoomAvailability::where('room_id', $roomId)
                ->where('available_from', '<=', $checkIn)
                ->where('available_to', '>=', $checkOut)
                ->first();

            if (!$availableSlot) {
                $validator->errors()->add('availability', 'This room is not available for the selected dates.');
            }
        });
    }
}
