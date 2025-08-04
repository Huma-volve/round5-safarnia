<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    protected $fillable = [
        'room_id',
        'available_from',
        'available_to'
    ];
    protected $table = 'room_availability_slots';

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
