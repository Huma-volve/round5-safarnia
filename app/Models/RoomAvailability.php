<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'available_from',
        'available_to',
        'discount'
    ];
    protected $table = 'room_availability_slots';

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
