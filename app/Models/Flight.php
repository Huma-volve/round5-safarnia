<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flight extends Model
{
    use HasFactory;
    protected $table = 'flights';

    public function flightBookings()
    {
        return $this->hasMany(FlightBooking::class);
    }

    public function flightSeats()
    {
        return $this->hasMany(FlightSeat::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
