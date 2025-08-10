<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSeat extends Model
{
    protected $table = 'flight_seats';

    protected $guarded = ['id'];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
