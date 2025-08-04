<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarCategory extends Model
{
    protected $fillable = ['name', 'description', 'image_url'];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
