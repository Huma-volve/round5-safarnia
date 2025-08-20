<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Room extends Model
{
    use HasFactory,Searchable;

    protected $fillable = [
        'hotel_id',
        'capacity',
        'bathroom_number',
        'area',
        'description',
        'price'
    ];
    protected $table = 'rooms';

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function bookings()
    {
        return $this->hasMany(RoomBooking::class);
    }

    public function room_availability()
    {
        return $this->hasMany(RoomAvailability::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function searchableAs()
    {
        return 'rooms';
    }
}
