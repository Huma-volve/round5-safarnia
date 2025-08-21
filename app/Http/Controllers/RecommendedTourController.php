<?php

namespace App\Http\Controllers;

use App\Http\Resources\TourResource;
use App\Models\Tour;
use Illuminate\Http\Request;

class RecommendedTourController extends Controller
{
    /**
     * Get recommended tours by rating
     */
    public function recommended()
    {
        $recommendedTours = Tour::where('is_recommended', true)
                               ->with(['category', 'availabilitySlots'])
                               ->orderByDesc('rating')
                               ->take(5)
                               ->get();
        
        return response()->json([
            'status' => true,
            'data' => TourResource::collection($recommendedTours)
        ]);
    }

    /**
     * Get top rated tours
     */
    public function topRated()
    {
        $topRatedTours = Tour::where('rating', '>=', 4.0)
                             ->with(['category', 'availabilitySlots'])
                             ->orderByDesc('rating')
                             ->take(10)
                             ->get();
        
        return response()->json([
            'status' => true,
            'data' => TourResource::collection($topRatedTours)
        ]);
    }

    /**
     * Get most viewed tours
     */
    public function mostViewed()
    {
        $mostViewedTours = Tour::with(['category', 'availabilitySlots'])
                              ->orderByDesc('views')
                              ->take(10)
                              ->get();
        
        return response()->json([
            'status' => true,
            'data' => TourResource::collection($mostViewedTours)
        ]);
    }

    /**
     * Get trending tours (combination of rating and views)
     */
    public function trending()
    {
        $trendingTours = Tour::with(['category', 'availabilitySlots'])
                            ->orderByRaw('(rating * 0.7) + (views * 0.3) DESC')
                            ->take(8)
                            ->get();
        
        return response()->json([
            'status' => true,
            'data' => TourResource::collection($trendingTours)
        ]);
    }

    /**
     * Get recommended tours by category
     */
    public function byCategory($categoryId)
    {
        $recommendedTours = Tour::where('category_id', $categoryId)
                               ->where('is_recommended', true)
                               ->with(['category', 'availabilitySlots'])
                               ->orderByDesc('rating')
                               ->take(5)
                               ->get();
        
        return response()->json([
            'status' => true,
            'data' => TourResource::collection($recommendedTours)
        ]);
    }
}
