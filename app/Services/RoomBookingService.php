<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\RoomBooking;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Auth;

class RoomBookingService
{
    public function MakeRoomBooking(array $data)
    {
        // التأكد من عدم وجود حجز متداخل على نفس الغرفة
        $availabilityBooking = RoomBooking::where('room_id', $data['room_id'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($data) {
                $query->whereBetween('check_in_date', [$data['check_in_date'], $data['check_out_date']])
                    ->orWhereBetween('check_out_date', [$data['check_in_date'], $data['check_out_date']]);
            })
            ->exists();
        if ($availabilityBooking) {
            throw new Exception("Room is already booked for the selected dates.");
        }

        return RoomBooking::create([
            'user_id' => Auth::id(),
            'room_id' => $data['room_id'],
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'adults_count' => $data['adults_count'],
            'children_count' => $data['children_count'] ?? 0,
            'infants_count' => $data['infants_count'] ?? 0,
        ]);
    }
    public function MyRoomBooking()
    {
        $userId=Auth::user()->id;
        $booking=RoomBooking::where('user_id',$userId)->get();
        return ApiResponse::sendResponse(200, 'your Room Booking retrieved successfully', $booking);
    }
}
