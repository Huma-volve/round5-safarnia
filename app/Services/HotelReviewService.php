<?php

namespace App\Services;

use App\Models\HotelReview;
use App\Models\Reservation;
use App\Models\RoomBooking;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ApiResponse;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

class HotelReviewService
{
    public function addReview(array $data)
    {
        $userId = Auth::user()->id;
        // Get room_id from input
        $roomId = $data['room_id'];

        // Fetch hotel_id based on room_id
        $room = Room::findOrFail($roomId);
        $hotelId = $room->hotel_id;
        // Optionally require a booking before allowing a review (default: disabled)
        $requireBooking = (bool) env('REQUIRE_BOOKING_FOR_REVIEW', false);
        if ($requireBooking) {
            $hasBooking = RoomBooking::where('user_id', $userId)
                ->whereHas('room', function ($query) use ($hotelId) {
                    $query->where('hotel_id', $hotelId);
                })
                ->exists();

            if (!$hasBooking) {
                return ApiResponse::sendResponse(403, 'You must have a Booking in this hotel to leave a review.');
            }
        }

        // Optional image upload
        if (isset($data['image'])) {
            $imagePath = $data['image']->store('images', 'public');
            $data['image'] = asset(Storage::url($imagePath));
        }

        // Create the review (using hotel_id extracted from room)
        $review = HotelReview::create([
            'hotel_id' => $hotelId,
            'user_id' => $userId,
            'rating' => $data['rating'],
            'review_text' => $data['review_text'],
            'image' => $data['image'] ?? null,
        ]);

        return ApiResponse::sendResponse(201, 'Review added successfully.', $review);
    }
    // عرض اخر 20 تقييم للأوتيل
    public function getReviewsForHotel($roomId)
    {
        $room = Room::findOrFail($roomId);
        $hotelId = $room->hotel_id;
        $reviews = HotelReview::where('hotel_id', $hotelId)
            ->with(['user:id,name,image'])
            ->latest()
            ->take(20)
            ->get();

        return ApiResponse::sendResponse(200, 'Hotel reviews retrieved successfully.', $reviews);
    }
}
