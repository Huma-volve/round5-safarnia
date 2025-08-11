<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable=['category_id','title','location','rating','description','price','image','views'];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function availabilitySlots()
    {
        return $this->hasMany(TourAvailabilitySlot::class);
    }

    
}