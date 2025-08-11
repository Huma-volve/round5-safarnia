<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
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
            'category_id' => $this->category_id,
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
            'price' => $this->price,
            'image' => asset('storage/' . $this->image),
            'views' => $this->views,
            'is_recommended' => $this->is_recommended,
            'rating' => $this->rating,
        ];
    }
}
