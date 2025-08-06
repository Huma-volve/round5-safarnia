<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Room;
use Faker\Extension\Helper;
use App\Helpers\ApiResponse;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HotelsService
{
    public function getRoomsForHotel($hotelId)
    {
        $today = Carbon::today();

        //  Get available rooms of the given hotel
        $slots = RoomAvailability::whereHas('rooms', function ($query) use ($hotelId) {
            $query->where('hotel_id', $hotelId);
        })
            ->whereDate('available_from', '<=', $today)
            ->whereDate('available_to', '>=', $today)
            ->with('rooms')
            ->get();

        $rooms = $slots->pluck('rooms')->unique('id')->values();

        return ApiResponse::sendResponse(200, 'Available rooms for hotel today', $rooms);
    }
    // public function getNearbyHotels()
    // {
    //     // Get the authenticated user's location to filter hotels based on it
    //     $userLocation = Auth::user()->country;
    //     // Search for hotels with a location that partially matches the user's location
    //     $hotels = Hotel::where('location', 'LIKE', '%' . $userLocation . '%')->get();

    //     return ApiResponse::sendResponse(200, 'Nearby hotels based on your location have been retrieved successfully', $hotels);
    // }

}
