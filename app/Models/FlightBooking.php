<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightBooking extends Model
{
    
    protected $table = 'flight_bookings';
    protected $guarded = ['id'];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function seats()
    {
        return $this->belongsTo(FlightSeat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
