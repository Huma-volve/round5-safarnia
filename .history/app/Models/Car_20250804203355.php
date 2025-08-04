<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'model', 'brand', 'daily_rate', 'seats', 'transmission',
        'fuel_type', 'has_ac', 'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(CarCategory::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilitySlots()
    {
        return $this->hasMany(AvailabilitySlot::class);
    }
}
