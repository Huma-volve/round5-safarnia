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
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'views' => $this->views,
            'is_recommended' => $this->is_recommended,
            'rating' => $this->rating,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Additional tour details
            'duration_hours' => $this->duration_hours,
            'max_group_size' => $this->max_group_size,
            'min_age' => $this->min_age,
            'difficulty_level' => $this->difficulty_level,
            'highlights' => $this->highlights ?? [],
            'included_services' => $this->included_services ?? [],
            'excluded_services' => $this->excluded_services ?? [],
            'what_to_bring' => $this->what_to_bring ?? [],
            'cancellation_policy' => $this->cancellation_policy,
            
            // Category information
            'category' => $this->whenLoaded('category', function() {
                return [
                    'id' => $this->category->id,
                    'title' => $this->category->title,
                    'description' => $this->category->description,
                    'image' => $this->category->image ? asset('storage/' . $this->category->image) : null,
                ];
            }),
            
            // Availability slots
            'availability_slots' => $this->whenLoaded('availabilitySlots', function() {
                return $this->availabilitySlots->map(function($slot) {
                    return [
                        'id' => $slot->id,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'available_seats' => $slot->available_seats,
                        'max_seats' => $slot->max_seats,
                        'is_available' => $slot->available_seats > 0 && $slot->start_time > now(),
                        'formatted_start_time' => $slot->start_time->format('Y-m-d H:i:s'),
                        'formatted_end_time' => $slot->end_time->format('Y-m-d H:i:s'),
                        'duration_hours' => $slot->start_time->diffInHours($slot->end_time),
                        'booking_count' => $slot->max_seats - $slot->available_seats,
                    ];
                });
            }),
            
            // Tour images
            'images' => $this->whenLoaded('images', function() {
                return $this->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'url' => asset('storage/' . $image->image_path),
                        'path' => $image->image_path,
                    ];
                });
            }),
            
            // Statistics and computed attributes
            'total_slots' => $this->whenLoaded('availabilitySlots', function() {
                return $this->availabilitySlots->count();
            }),
            'available_slots_count' => $this->available_slots_count,
            'total_capacity' => $this->total_capacity,
            'total_available_seats' => $this->total_available_seats,
            'has_available_slots' => $this->has_available_slots,
            'next_available_slot' => $this->when($this->next_available_slot, function() {
                return [
                    'id' => $this->next_available_slot->id,
                    'start_time' => $this->next_available_slot->start_time->format('Y-m-d H:i:s'),
                    'end_time' => $this->next_available_slot->end_time->format('Y-m-d H:i:s'),
                    'available_seats' => $this->next_available_slot->available_seats,
                ];
            }),
            
            // Booking statistics
            'total_bookings' => $this->whenLoaded('bookings', function() {
                return $this->bookings->count();
            }),
            'recent_bookings' => $this->whenLoaded('bookings', function() {
                return $this->bookings->take(5)->map(function($booking) {
                    return [
                        'id' => $booking->id,
                        'status' => $booking->status,
                        'seats_count' => $booking->seats_count,
                        'total_price' => $booking->total_price,
                        'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }),

            // Related Activities
            'top_activities' => $this->whenLoaded('topActivities', function() {
                return ActivityResource::collection($this->topActivities->take(5));
            }),
            'popular_activities' => $this->whenLoaded('popularActivities', function() {
                return ActivityResource::collection($this->popularActivities->take(5));
            }),
            'recommended_activities' => $this->whenLoaded('recommendedActivities', function() {
                return ActivityResource::collection($this->recommendedActivities->take(5));
            }),
            'related_activities_count' => $this->whenLoaded('relatedActivities', function() {
                return $this->relatedActivities->count();
            }),
        ];
    }
}
