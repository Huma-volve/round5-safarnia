<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tour_title' => $this->tourSlot->tour->title ?? 'N/A',
            'slot_time' => $this->tourSlot->start_time . ' - ' . $this->tourSlot->end_time,
            'status' => $this->status,
            'seats_count' => $this->seats_count,
            'total_price' => $this->total_price,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
