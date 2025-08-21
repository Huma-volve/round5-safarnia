<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use App\Models\TourAvailabilitySlot;
use App\Http\Resources\TourAvailabilitySlotResource;
use Illuminate\Support\Facades\DB;

class TourAvailSlotController extends Controller
{
    /**
     * Display all available slots for a tour
     */
    public function index(Tour $tour)
    {
        $slots = $tour->availabilitySlots()
                      ->where('available_seats', '>', 0)
                      ->where('start_time', '>', now())
                      ->orderBy('start_time')
                      ->get();

        return response()->json([
            'status' => true,
            'data' => TourAvailabilitySlotResource::collection($slots)
        ]);
    }

    /**
     * Create a new availability slot (Admin only)
     */
    public function store(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'available_seats' => 'required|integer|min:1|max:100',
            'max_seats' => 'sometimes|integer|min:1|max:100',
        ]);

        // Set max_seats if not provided
        if (!isset($data['max_seats'])) {
            $data['max_seats'] = $data['available_seats'];
        }

        $slot = $tour->availabilitySlots()->create($data);

        return response()->json([
            'status' => true,
            'message' => 'Availability slot created successfully',
            'data' => new TourAvailabilitySlotResource($slot)
        ]);
    }

    /**
     * Update an availability slot (Admin only)
     */
    public function update(Request $request, TourAvailabilitySlot $slot)
    {
        $data = $request->validate([
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'available_seats' => 'sometimes|required|integer|min:1|max:100',
            'max_seats' => 'sometimes|integer|min:1|max:100',
        ]);

        // Check if slot can be updated (no active bookings)
        if ($slot->bookings()->whereNotIn('status', ['cancelled'])->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot update slot with active bookings'
            ], 422);
        }

        $slot->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Availability slot updated successfully',
            'data' => new TourAvailabilitySlotResource($slot)
        ]);
    }

    /**
     * Delete an availability slot (Admin only)
     */
    public function destroy(TourAvailabilitySlot $slot)
    {
        // Check if slot can be deleted (no active bookings)
        if ($slot->bookings()->whereNotIn('status', ['cancelled'])->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete slot with active bookings'
            ], 422);
        }

        $slot->delete();

        return response()->json([
            'status' => true,
            'message' => 'Availability slot deleted successfully'
        ]);
    }

    /**
     * Get slots by date range
     */
    public function getByDateRange(Request $request, Tour $tour)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $slots = $tour->availabilitySlots()
                      ->whereBetween('start_time', [$request->start_date, $request->end_date])
                      ->where('available_seats', '>', 0)
                      ->orderBy('start_time')
                      ->get();

        return response()->json([
            'status' => true,
            'data' => TourAvailabilitySlotResource::collection($slots)
        ]);
    }

    /**
     * Get all slots for admin management
     */
    public function getAllSlots(Tour $tour)
    {
        $slots = $tour->availabilitySlots()
                      ->with(['bookings'])
                      ->orderBy('start_time')
                      ->get();

        return response()->json([
            'status' => true,
            'data' => TourAvailabilitySlotResource::collection($slots)
        ]);
    }

    /**
     * Bulk create slots for a tour (Admin only)
     */
    public function bulkCreate(Request $request, Tour $tour)
    {
        $request->validate([
            'slots' => 'required|array|min:1',
            'slots.*.start_time' => 'required|date|after:now',
            'slots.*.end_time' => 'required|date|after:start_time',
            'slots.*.available_seats' => 'required|integer|min:1|max:100',
            'slots.*.max_seats' => 'sometimes|integer|min:1|max:100',
        ]);

        DB::beginTransaction();
        try {
            $createdSlots = [];
            
            foreach ($request->slots as $slotData) {
                if (!isset($slotData['max_seats'])) {
                    $slotData['max_seats'] = $slotData['available_seats'];
                }
                
                $slot = $tour->availabilitySlots()->create($slotData);
                $createdSlots[] = $slot;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($createdSlots) . ' slots created successfully',
                'data' => TourAvailabilitySlotResource::collection(collect($createdSlots))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create slots'
            ], 500);
        }
    }
}
