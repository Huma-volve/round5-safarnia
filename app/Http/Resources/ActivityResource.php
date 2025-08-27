<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location,
            'country' => $this->country,
            'city' => $this->city,
            'rating' => $this->rating,
            'price_range' => $this->price_range,
            'formatted_price_range' => $this->formatted_price_range,
            'duration_hours' => $this->duration_hours,
            'category' => $this->category,
            'image' => $this->image,
            'highlights' => $this->highlights ?? [],
            'included_services' => $this->included_services ?? [],
            'excluded_services' => $this->excluded_services ?? [],
            'what_to_bring' => $this->what_to_bring ?? [],
            'best_time_to_visit' => $this->best_time_to_visit,
            'difficulty_level' => $this->difficulty_level,
            'difficulty_level_arabic' => $this->difficulty_level_arabic,
            'min_age' => $this->min_age,
            'max_group_size' => $this->max_group_size,
            'views' => $this->views,
            'is_popular' => $this->is_popular,
            'is_recommended' => $this->is_recommended,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
