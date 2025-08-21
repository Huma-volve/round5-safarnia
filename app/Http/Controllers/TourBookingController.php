<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourBookingResource;
use App\Models\TourBooking;
use App\Models\TourAvailabilitySlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TourBookingController extends Controller
{
    /**
     * Create a new tour booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_slot_id' => 'required|exists:tour_availability_slots,id',
            'seats_count' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

                // Check if slot is available
        $slot = TourAvailabilitySlot::with('tour')->findOrFail($validated['tour_slot_id']);

        if ($slot->available_seats < $validated['seats_count']) {
            return response()->json([
                'status' => false,
                'message' => 'Not enough seats available'
            ], 422);
        }

        if ($slot->start_time <= now()) {
            return response()->json([
                'status' => false,
                'message' => 'This time slot is not available'
            ], 422);
        }

        // Check if tour exists
        if (!$slot->tour) {
            return response()->json([
                'status' => false,
                'message' => 'Tour not found for this slot'
            ], 422);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Calculate total price
        $totalPrice = $slot->tour->price * $validated['seats_count'];

        DB::beginTransaction();
        try {
            // Create booking
            $booking = TourBooking::create([
                'user_id' => auth()->id(),
                'tour_slot_id' => $validated['tour_slot_id'],
                'status' => 'pending',
                'seats_count' => $validated['seats_count'],
                'total_price' => $totalPrice,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update available seats
            $slot->decrement('available_seats', $validated['seats_count']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tour booked successfully',
                'data' => new TourBookingResource($booking->load(['tourSlot.tour', 'user']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create booking'
            ], 500);
        }
    }

    /**
     * Get booking details
     */
    public function show($id)
    {
        $booking = TourBooking::with(['tourSlot.tour', 'user'])
                              ->where('id', $id)
                              ->where('user_id', auth()->id())
                              ->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => new TourBookingResource($booking)
        ]);
    }

    /**
     * Update an existing booking
     */
    public function update(Request $request, $id)
    {
        $booking = TourBooking::where('id', $id)
                              ->where('user_id', auth()->id())
                              ->firstOrFail();

        $validated = $request->validate([
            'tour_slot_id' => 'sometimes|required|exists:tour_availability_slots,id',
            'seats_count' => 'sometimes|required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if booking can be updated
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'status' => false,
                'message' => 'This booking cannot be updated'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // If changing slot, release old seats and book new ones
            if (isset($validated['tour_slot_id']) && $validated['tour_slot_id'] != $booking->tour_slot_id) {
                $oldSlot = $booking->tourSlot;
                $oldSlot->increment('available_seats', $booking->seats_count ?? 1);

                $newSlot = TourAvailabilitySlot::findOrFail($validated['tour_slot_id']);
                if ($newSlot->available_seats < ($validated['seats_count'] ?? $booking->seats_count)) {
                    throw new \Exception('New slot is not available');
                }
                $newSlot->decrement('available_seats', $validated['seats_count'] ?? $booking->seats_count);
            }

            // If changing seats count, adjust availability
            if (isset($validated['seats_count']) && $validated['seats_count'] != $booking->seats_count) {
                $currentSlot = $booking->tourSlot;
                $difference = $validated['seats_count'] - $booking->seats_count;

                if ($difference > 0) {
                    if ($currentSlot->available_seats < $difference) {
                        throw new \Exception('Not enough seats available');
                    }
                    $currentSlot->decrement('available_seats', $difference);
                } else {
                    $currentSlot->increment('available_seats', abs($difference));
                }
            }

            // Update booking
            $booking->update($validated);

            // Recalculate total price if seats count changed
            if (isset($validated['seats_count'])) {
                $booking->total_price = $booking->tourSlot->tour->price * $validated['seats_count'];
                $booking->save();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Booking updated successfully',
                'data' => new TourBookingResource($booking->load(['tourSlot.tour', 'user']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get user's tour bookings
     */
    public function myBookings()
    {
        $bookings = TourBooking::with(['tourSlot.tour', 'user'])
                               ->where('user_id', auth()->id())
                               ->orderBy('created_at', 'desc')
                               ->get();

        return response()->json([
            'status' => true,
            'data' => TourBookingResource::collection($bookings)
        ]);
    }

    public function cancel($id)
    {
        // البحث عن الحجز الخاص بالمستخدم الحالي
        $booking = TourBooking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // تحقق من حالة الحجز ووقت بداية الرحلة
        if (in_array($booking->status, ['pending', 'confirmed']) && $booking->tourSlot->start_time > now()) {

            // إعادة المقاعد المتاحة
            $booking->tourSlot->increment('available_seats', $booking->seats_count ?? 1);

            // تحديث حالة الحجز إلى "ملغي"
            $booking->update(['status' => 'cancelled']);

            return response()->json([
                'status' => true,
                'message' => 'Booking cancelled successfully',
            ]);
        }

        // لو الحجز بدأ بالفعل
        if ($booking->tourSlot->start_time <= now()) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot cancel a booking after the tour has started.',
            ], 400);
        }

        // لو الحجز بالفعل ملغي أو منتهي
        if ($booking->status === 'cancelled') {
            return response()->json([
                'status' => false,
                'message' => 'This booking is already cancelled.',
            ], 400);
        }

        // لو الحجز مكتمل أو في حالة أخرى غير قابلة للإلغاء
        return response()->json([
            'status' => false,
            'message' => 'This booking cannot be cancelled at this stage.',
        ], 400);
    }
}
