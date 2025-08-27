<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location',
        'country',
        'city',
        'rating',
        'price_range',
        'duration_hours',
        'category',
        'image',
        'highlights',
        'included_services',
        'excluded_services',
        'what_to_bring',
        'best_time_to_visit',
        'difficulty_level',
        'min_age',
        'max_group_size',
        'views',
        'is_popular',
        'is_recommended'
    ];

    protected $casts = [
        'rating' => 'float',
        'price_range' => 'array',
        'duration_hours' => 'integer',
        'highlights' => 'array',
        'included_services' => 'array',
        'excluded_services' => 'array',
        'what_to_bring' => 'array',
        'views' => 'integer',
        'is_popular' => 'boolean',
        'is_recommended' => 'boolean',
    ];

    /**
     * Get tours in the same location
     */
    public function relatedTours()
    {
        return $this->hasMany(Tour::class, 'location', 'location');
    }

    /**
     * Get tours in the same country
     */
    public function toursInCountry()
    {
        return $this->hasMany(Tour::class, 'location', 'country');
    }

    /**
     * Scope for popular activities
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope for recommended activities
     */
    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    /**
     * Scope for activities by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', '%' . $location . '%')
                    ->orWhere('city', 'like', '%' . $location . '%')
                    ->orWhere('country', 'like', '%' . $location . '%');
    }

    /**
     * Scope for top rated activities
     */
    public function scopeTopRated($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Get price range as formatted string
     */
    public function getFormattedPriceRangeAttribute()
    {
        if (empty($this->price_range)) {
            return 'Contact for pricing';
        }
        
        $min = $this->price_range['min'] ?? 0;
        $max = $this->price_range['max'] ?? 0;
        
        if ($min == $max) {
            return '$' . number_format($min, 2);
        }
        
        return '$' . number_format($min, 2) . ' - $' . number_format($max, 2);
    }

    /**
     * Get difficulty level in Arabic
     */
    public function getDifficultyLevelArabicAttribute()
    {
        $levels = [
            'easy' => 'easy',
            'moderate' => 'moderate',
            'challenging' => 'challenging',
            'expert' => 'expert'
        ];
        
        return $levels[$this->difficulty_level] ?? $this->difficulty_level;
    }
}
