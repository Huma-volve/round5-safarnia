<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourAvailabilitySlot extends Model
{
    protected $fillable=['tour_id','start_time','available_seats','end_time'];

        public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings()
    {
        return $this->hasMany(TourBooking::class, 'tour_slot_id');
    }
}
