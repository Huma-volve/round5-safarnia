<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomBooking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'infants_count',
        'children_count',
        'adults_count',
        'status',
        'check_in_date',
        'check_out_date'
    ];
    protected $table = 'room_booking';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
