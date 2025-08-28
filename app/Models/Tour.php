<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'location',
        'rating',
        'description',
        'price',
        'image',
        'views',
        'is_recommended',
        'duration_hours',
        'max_group_size',
        'min_age',
        'difficulty_level',
        'highlights',
        'included_services',
        'excluded_services',
        'what_to_bring',
        'cancellation_policy'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'float',
        'views' => 'integer',
        'is_recommended' => 'boolean',
        'duration_hours' => 'integer',
        'max_group_size' => 'integer',
        'min_age' => 'integer',
        'highlights' => 'array',
        'included_services' => 'array',
        'excluded_services' => 'array',
        'what_to_bring' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function availabilitySlots()
    {
        return $this->hasMany(TourAvailabilitySlot::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
    public function bookings()
    {
        return $this->hasManyThrough(TourBooking::class, TourAvailabilitySlot::class, 'tour_id', 'tour_slot_id', 'id', 'id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get available slots count
     */
    public function getAvailableSlotsCountAttribute()
    {
        return $this->availabilitySlots()
                    ->where('available_seats', '>', 0)
                    ->where('start_time', '>', now())
                    ->count();
    }

    /**
     * Get total capacity
     */
    public function getTotalCapacityAttribute()
    {
        return $this->availabilitySlots()->sum('max_seats');
    }

    /**
     * Get total available seats
     */
    public function getTotalAvailableSeatsAttribute()
    {
        return $this->availabilitySlots()
                    ->where('start_time', '>', now())
                    ->sum('available_seats');
    }

    /**
     * Check if tour has available slots
     */
    public function getHasAvailableSlotsAttribute()
    {
        return $this->available_slots_count > 0;
    }

    /**
     * Get next available slot
     */
    public function getNextAvailableSlotAttribute()
    {
        return $this->availabilitySlots()
                    ->where('available_seats', '>', 0)
                    ->where('start_time', '>', now())
                    ->orderBy('start_time')
                    ->first();
    }

    /**
     * Get related activities in the same location
     */
    public function relatedActivities()
    {
        return $this->hasMany(Activity::class, 'location', 'location');
    }

    /**
     * Get top activities in the same location
     */
    public function topActivities()
    {
        return $this->hasMany(Activity::class, 'location', 'location')
                    ->where('rating', '>=', 4.0)
                    ->orderBy('rating', 'desc')
                    ->orderBy('views', 'desc');
    }

    /**
     * Get popular activities in the same location
     */
    public function popularActivities()
    {
        return $this->hasMany(Activity::class, 'location', 'location')
                    ->where('is_popular', true)
                    ->orderBy('rating', 'desc');
    }

    /**
     * Get recommended activities in the same location
     */
    public function recommendedActivities()
    {
        return $this->hasMany(Activity::class, 'location', 'location')
                    ->where('is_recommended', true)
                    ->orderBy('rating', 'desc');
    }
}
