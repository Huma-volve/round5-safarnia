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
        protected SearchHotelService $searchHotelService
    ) {}
    // عرض الغرف المتاحة في هذا اليوم في هذا الاوتيل
    public function getRoomsForHotel($hotelId)
    {
        return $this->hotelsService->getAvailableRooms($hotelId);
    }

    public function getRooms()
    {
        return $this->hotelsService->getAvailableRooms();
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
    public function searchHotels(Request $request)
    {
        $search = $request->input('key'); // الكلمة اللي المستخدم بيدور عليها
        return  $this->searchHotelService->searchHotels($search);
    }
    public function searchRooms(Request $request)
    {
        $search = $request->input('key'); // الكلمة اللي المستخدم بيدور عليها
        return  $this->searchHotelService->searchRooms($search);
    }

}
