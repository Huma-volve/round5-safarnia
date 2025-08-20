<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Hotel extends Model
{
    use HasFactory,Searchable;

    protected $fillable = [
        'name',
        'location',
        'category_id'
    ];
    protected $table = 'hotels';

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reviews()
    {
        return $this->hasMany(HotelReview::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function searchableAs()
    {
        return 'hotels';
    }
}
