<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourBooking extends Model
{
    protected $fillable = ['user_id', 'tour_slot_id', 'status', 'seats_count', 'total_price', 'notes'];

    protected $casts = [
        'seats_count' => 'integer',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tourSlot()
    {
        return $this->belongsTo(TourAvailabilitySlot::class, 'tour_slot_id');
    }

    public function tour()
    {
        return $this->hasOneThrough(Tour::class, TourAvailabilitySlot::class, 'id', 'tour_id', 'tour_slot_id', 'id');
    }
}