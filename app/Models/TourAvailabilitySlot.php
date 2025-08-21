<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourAvailabilitySlot extends Model
{
    protected $fillable = ['tour_id', 'start_time', 'end_time', 'available_seats', 'max_seats'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'available_seats' => 'integer',
        'max_seats' => 'integer',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings()
    {
        return $this->hasMany(TourBooking::class, 'tour_slot_id');
    }
}
