<?php

namespace App\Http\Controllers\Api;

use App\Models\Flight;
use App\Models\FlightSeat;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\FlightBooking;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\FlightBookingRequest;

class FlightBookingController extends Controller
{
    //

    public function store(FlightBookingRequest $request)
    {
        $validatedData = $request->validated();

        $flight = Flight::findOrFail($validatedData['flight_id']);

        return DB::transaction(function () use ($validatedData, $flight) {
            $seat = FlightSeat::where('id', $validatedData['seat_id'])
                ->where('status', 'available')
                ->firstOrFail();

            // Ensure selected seat belongs to the same flight
            if ((int)($seat->flight_id) !== (int)($flight->id)) {
                return ApiResponse::sendResponse(422, 'Selected seat does not belong to the given flight');
            }

            $booking = FlightBooking::create([
                'user_id' => auth()->id(),
                'flight_id' => $flight->id,
                'seat_id' => $seat->id,
                'booking_date' => now()->toDateString(),
                'total_price' => $flight->price,
                'status' => 'pending',
            ]);

            $seat->update(['status' => 'booked']);

            return ApiResponse::sendResponse(201, 'Flight booking created successfully', [
                'booking' => $booking,
                'seat' => $seat,
            ]);
        });
    }

    public function myBookingsFlight()
    {
        $bookings = FlightBooking::where('user_id', auth()->id())
            ->get();

        return ApiResponse::sendResponse(200, 'User flight bookings retrieved successfully', $bookings);
    }

    public function update(Request $request, $id)
    {
        $booking = FlightBooking::findOrFail($id);

        if ($booking->user_id !== auth()->id()) {
            return ApiResponse::sendResponse(403, 'Unauthorized action');
        }

        $validatedData = $request->validate([
            'seat_id' => [
                'required',
                Rule::exists('flight_seats', 'id')->where('status', 'available')
            ],
        ]);
        
        $oldSeat = FlightSeat::findOrFail($booking->seat_id);
        $oldSeat->update(['status' => 'available']);

        $booking->update([
            'seat_id' => $validatedData['seat_id'],
        ]);
        
        $newSeat = FlightSeat::findOrFail($validatedData['seat_id']);
        // Ensure new seat belongs to the same flight
        if ((int)$newSeat->flight_id !== (int)$booking->flight_id) {
            // revert old seat to booked and return error
            $oldSeat->update(['status' => 'booked']);
            return ApiResponse::sendResponse(422, 'Selected seat does not belong to the booked flight');
        }

        $newSeat->update(['status' => 'booked']);
        return ApiResponse::sendResponse(200, 'Flight booking updated successfully', $booking);
    }

    public function destroy($id)
    {
        $booking = FlightBooking::findOrFail($id);

        if ($booking->user_id !== auth()->id()) {
            return ApiResponse::sendResponse(403, 'Unauthorized action');
        }

        $booking->update(['status' => 'cancelled']);

        $seat = FlightSeat::findOrFail($booking->seat_id);
        $seat->update(['status' => 'available']);

        return ApiResponse::sendResponse(200, 'Flight booking cancelled successfully', $booking);
    }
}
