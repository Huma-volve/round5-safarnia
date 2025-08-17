<?php
namespace App\Http\Controllers;

 use App\Http\Controllers\Controller;
 use App\Http\Resources\TourBookingResource;
 use App\Models\TourBooking;
 use Illuminate\Http\Request;

    class TourBookingController extends Controller
    {
    public function store(Request $request)
        {
    $validated = $request->validate([
    'tour_slot_id' => 'required|exists:tour_slots,id',
    ]);

    $booking = TourBooking::create([
    'user_id' => auth()->id(),
    'tour_slot_id' => $validated['tour_slot_id'],
    'status' => 'pending',
    ]);

    return new TourBookingResource($booking);
    }
    // تحديث
    public function update(Request $request, $id)
    {
    $booking = TourBooking::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
    $validated = $request->validate([
    'tour_slot_id' => 'required|exists:tour_slots,id',
    ]);
    $booking->update(['tour_slot_id' => $validated['tour_slot_id']]);
    return response()->json(['message' => 'Booking updated successfully']);
    }
// حذف
 public function destroy($id){
        $booking = TourBooking::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
            $booking->delete();
        return response()->json(['message' => 'Booking deleted successfully']);
        }
//انشاء حجر جديد
public function myBookings()
        {
$bookings = TourBooking::with('slot.tour')->where('user_id', auth()->id())->get();

return TourBookingResource::collection($bookings);
    }
        }
