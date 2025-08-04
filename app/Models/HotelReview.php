<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelReview extends Model
{
    protected $fillable = [
        'hotel_id',
        'user_id',
        'rating',
        'review_text'
    ];
    protected $table = 'hotel_reviews';

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
