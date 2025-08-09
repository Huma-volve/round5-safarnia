<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\HotelsService;
use App\Services\SearchHotelService;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function __construct(
        protected HotelsService $hotelsService,
    ) {}
    // عرض الغرف المتاحة في هذا اليوم في هذا الاوتيل
    public function getRoomsForHotel($hotelId)
    {
        return $this->hotelsService->getAvailableRooms($hotelId);
    }
    // عرض تفاصيل الغرفة
    public function getRoomDetails($roomId)
    {
        return $this->hotelsService->getRoomDetails($roomId);
    }
    // عرض الفنادق
    public function getHotels()
    {
        return $this->hotelsService->getNearbyHotels();
    }

}
