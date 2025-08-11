<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoomBookingRequest;
use App\Http\Requests\Api\UpdateRoomBookingRequest;
use App\Services\RoomBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomBookingController extends Controller
{
    public function __construct(
        protected RoomBookingService $BookingService,
    ) {}
    // get all user room booking
    public function getUserRoomBookings()
    {
        return $this->BookingService->MyRoomBooking();
    }
    // create a new booking
    public function createRoomBooking(RoomBookingRequest $request)
    {
        $data = $request->validated();
        return $this->BookingService->MakeRoomBooking($data);
    }
    // cancel
    public function cancelRoomBooking($bookingId)
    {
        return $this->BookingService->cancelMyRoomBooking($bookingId);
    }
    // update
    public function updateRoomBooking(UpdateRoomBookingRequest $request, $bookingId)
    {
        $data = $request->validated();
        return $this->BookingService->updateRoomBooking($bookingId, $data);
    }
}
