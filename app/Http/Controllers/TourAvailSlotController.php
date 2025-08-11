<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use App\Models\TourAvailabilitySlot;
use App\Http\Resources\TourAvailabilitySlotResource;

class TourAvailSlotController extends Controller
{
    // إنشاء Slot جديد
    public function store(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'available_seats' => 'required|integer|min:1',
        ]);

        $slot = $tour->availabilitySlots()->create($data);

        return new TourAvailabilitySlotResource($slot);
    }

    // عرض كل Slots لرحلة معينة
    public function index(Tour $tour)
    {
        return TourAvailabilitySlotResource::collection(
            $tour->availabilitySlots()->orderBy('start_time')->get()
        );
    }

    // تحديث Slot
    public function update(Request $request, TourAvailabilitySlot $slot)
    {
        $data = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'available_seats' => 'sometimes|integer|min:1',
        ]);

        $slot->update($data);

        return new TourAvailabilitySlotResource($slot);
    }

    // حذف Slot
    public function destroy(TourAvailabilitySlot $slot)
    {
        $slot->delete();

        return response()->json(['message' => 'Slot deleted']);
    }
}
