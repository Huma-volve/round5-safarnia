<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Room;
use Faker\Extension\Helper;
use App\Helpers\ApiResponse;
use App\Models\HotelReview;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HotelsService
{
    public function getAvailableRooms($hotelId)
    {
        $today = Carbon::today();

        //  Get available rooms of the given hotel
        $slots = RoomAvailability::whereHas('room', function ($query) use ($hotelId) {
            $query->where('hotel_id', $hotelId);
        })
            ->whereDate('available_from', '<=', $today)
            ->whereDate('available_to', '>=', $today)
            ->with('room')
            ->get();

        $rooms = $slots->pluck('room')->unique('id')->values();

        $data = $rooms->map(
            function ($room) use ($slots) {
                $firstImage = $room->images->first();

                $slot = $slots->firstWhere('room_id', $room->id);
                $discount = $slot ? $slot->discount : null;
                return [
                    'id' => $room->id,
                    'price' => $room->price,
                    'discount' => $discount,
                    'image' => $firstImage ? $firstImage->image_path: null,
                ];
            }
        );
        return ApiResponse::sendResponse(200, 'Available rooms for hotel today', $data);
    }
    // تفاصيل الغرفة
    public function getRoomDetails($roomId)
    {
        $today = Carbon::today();

        $room = Room::with('images')->findOrFail($roomId);

        $slot = RoomAvailability::where('room_id', $roomId)
            ->whereDate('available_from', '<=', $today)
            ->whereDate('available_to', '>=', $today)
            ->first();

        $discount = $slot ? $slot->discount : null;
        $firstImage = $room->images->first();

        //  حساب تقييم الفندق وعدد التقييمات
        $totalReviews = HotelReview::where('hotel_id', $room->hotel_id)->count();
        $averageRating = HotelReview::where('hotel_id', $room->hotel_id)->avg('rating');

        $data = [
            'id' => $room->id,
            'description' => $room->description,
            'price' => $room->price,
            'area' => $room->area,
            'capacity' => $room->capacity,
            'bathroom_number' => $room->bathroom_number,
            'image' => $firstImage ? $firstImage->image_path : null,
            'discount' => $discount,
            'average_rating' => round($averageRating, 2),
            'total_reviews' => $totalReviews,
        ];

        return ApiResponse::sendResponse(200, 'Room details retrieved successfully', $data);
    }

    public function getNearbyHotels()
    {
        $userLocation = Auth::user()->country ?? null;
        if ($userLocation) {
            $hotels = Hotel::where('location', 'LIKE', '%' . $userLocation . '%')
                ->with(['images', 'reviews'])
                ->get();
        } else {
            $hotels = Hotel::with(['images', 'reviews'])->get();
        }
        $data = $hotels->map(function ($hotel) {
            // حساب متوسط التقييم
            $averageRating = HotelReview::where('hotel_id', $hotel->id)->avg('rating');
            $firstImage = $hotel->images->first();
            return [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'location' => $hotel->location,
                'image' => $firstImage ? $firstImage->image_path : null,
                'average_rating' => round($averageRating, 2),
            ];
        });
        return ApiResponse::sendResponse(200, 'Nearby hotels based on your location have been retrieved successfully', $data);
    }
}
