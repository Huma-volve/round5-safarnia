<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourReview extends Model
{
     protected $fillable = [
        'tour_id',
        'user_id',
        'rating',
        'review'
    ];

    // Link to the Tour (a review belongs to a tour)
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    // Link to the User (a review belongs to a user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
