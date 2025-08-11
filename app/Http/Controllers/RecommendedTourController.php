<?php

namespace App\Http\Controllers;

use App\Http\Resources\TourResource;
use App\Models\Tour;
use Illuminate\Http\Request;

class RecommendedTourController extends Controller
{
    public function recommended(){
        $recommendedtour = Tour::orderByDesc('rating')->take(5)->get();
        return TourResource::collection( $recommendedtour);
    }
}
