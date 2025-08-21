<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['title', 'description', 'image'];

    protected $casts = [
        'image' => 'string',
    ];

    public function tours()
    {
        return $this->hasMany(Tour::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    /**
     * Get tours with availability slots
     */
    public function toursWithSlots()
    {
        return $this->tours()->with(['availabilitySlots' => function($query) {
            $query->where('available_seats', '>', 0);
        }]);
    }

    /**
     * Get recommended tours in this category
     */
    public function recommendedTours()
    {
        return $this->tours()->where('is_recommended', true);
    }

    /**
     * Get top rated tours in this category
     */
    public function topRatedTours($minRating = 4.0)
    {
        return $this->tours()->where('rating', '>=', $minRating)->orderBy('rating', 'desc');
    }
}
