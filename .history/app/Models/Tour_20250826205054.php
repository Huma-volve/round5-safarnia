<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = ['category_id', 'title', 'location', 'rating', 'description', 'price', 'image', 'views', 'is_recommended'];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'float',
        'views' => 'integer',
        'is_recommended' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function availabilitySlots()
    {
        return $this->hasMany(TourAvailabilitySlot::class);
    }
}