<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoomBookingRequest;
use App\Services\RoomBookingService;
use Illuminate\Http\Request;

class RoomBookingController extends Controller
{
    public function __construct(
        protected RoomBookingService $BookingService,
    ) {}

    public function getUserRoomBookings()
    {
        return $this->BookingService->MyRoomBooking();
    }
    public function createRoomBooking(RoomBookingRequest $request)
    {
        $data = $request->validated();
        return $this->BookingService->MakeRoomBooking($data);
    }
}
