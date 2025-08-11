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
          'tour_title' => $this->slot->tour->title ?? 'N/A',
            'slot_time' => $this->slot->start_time . ' - ' . $this->slot->end_time,
            'status' => $this->status,
        ];
    }
}
