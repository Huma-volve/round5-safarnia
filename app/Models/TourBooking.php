<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourBooking extends Model
{
    protected $fillable=['user_id','tour_slot_id','status'];
        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tourSlot()
    {
        return $this->belongsTo(TourAvailabilitySlot::class, 'tour_slot_id');
    }
}