<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\Hotel;
use App\Models\HotelReview;
use App\Models\Room;

class SearchHotelService
{
    public function searchHotels(string $search)
    {
        // البحث باستخدام Scout
        $hotels = Hotel::search($search)->get()

        // إضافة متوسط التقييم لكل فندق
            ->map(function ($hotel) {
            $hotel->average_rating = round($hotel->reviews()->avg('rating') ?? 0, 2);
            // جلب صورة الفندق
            $hotel->image_url = optional($hotel->images->first(), function ($image) {
                return $image->image_path;
            });
            unset($hotel->images);
            return $hotel;
            // ترتيب النتائج من الأعلى تقييم
        })->sortByDesc('average_rating')->values();

        return ApiResponse::sendResponse(200, "This is the result of your search", $hotels);
    }
    // البحث في الغرف
    public function searchRooms(string $search)
    {
        $roomsQuery = Room::query();

        if (is_numeric($search) && strlen($search) === 1) {
            // إذا رقم من خانة واحدة → اعتبره capacity
            $roomsQuery->where('capacity', $search);
        } elseif (is_numeric($search)) {
            // إذا رقم من خانتين أو أكثر → اعتبره سعر
            $price = (int) $search;
            $roomsQuery->whereBetween('price', [$price - 50, $price + 50]);
        } elseif ($this->isValidDate($search)) {
            // إذا المستخدم كتب تاريخ، نبحث عن الغرف المتاحة في هذا التاريخ
            $date = $search;
            $roomsQuery->whereHas('room_availability', function ($q) use ($date) {
                $q->where('available_from', '<=', $date)
                    ->where('available_to', '>=', $date);
            })->orderByDesc('price'); // ترتيب من الأعلى للأقل
        } else {
            // بحث نصي باستخدام Scout
            return ApiResponse::sendResponse(
                200,
                "This is the result of your search",
                Room::search($search)->get()
            );
        }
        // جلب النتائج مع صورة الغرفة
        $rooms = $roomsQuery->orderByDesc('price')->get()->map(function ($room) {
            $room->image_url = optional($room->images->first(), function ($image) {
                return $image->image_path;
            });
            unset($room->images);
            return $room;
        });
        return ApiResponse::sendResponse(200, "This is the result of your search", $rooms);
    }
    /**
     * التحقق إذا القيمة تاريخ صحيح
     */
    private function isValidDate($date)
    {
        return (bool) strtotime($date);
    }
}
