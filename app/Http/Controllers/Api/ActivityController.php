<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities
     */
    public function index(Request $request)
    {
        $query = Activity::query();

        // Search by keyword
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->byLocation($request->location);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by difficulty level
        if ($request->has('difficulty_level') && $request->difficulty_level) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->whereRaw("JSON_EXTRACT(price_range, '$.min') >= ?", [$request->min_price]);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->whereRaw("JSON_EXTRACT(price_range, '$.max') <= ?", [$request->max_price]);
        }

        // Filter by rating
        if ($request->has('min_rating') && $request->min_rating) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'rating');
        $sortOrder = $request->get('sort_order', 'desc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderByRaw("JSON_EXTRACT(price_range, '$.min') " . $sortOrder);
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            case 'views':
                $query->orderBy('views', $sortOrder);
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            default:
                $query->orderBy('rating', $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $activities = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => ActivityResource::collection($activities)
        ]);
    }

    /**
     * Display the specified activity
     */
    public function show(Activity $activity)
    {
        // Increment views
        $activity->increment('views');
        
        return response()->json([
            'status' => true,
            'message' => 'Activity details retrieved successfully',
            'data' => new ActivityResource($activity)
        ]);
    }

    /**
     * Get top rated activities
     */
    public function getTopRated()
    {
        $activities = Activity::topRated()
                             ->orderBy('rating', 'desc')
                             ->orderBy('views', 'desc')
                             ->take(10)
                             ->get();
        
        return response()->json([
            'status' => true,
            'data' => ActivityResource::collection($activities)
        ]);
    }

    /**
     * Get popular activities
     */
    public function getPopular()
    {
        $activities = Activity::popular()
                             ->orderBy('rating', 'desc')
                             ->take(10)
                             ->get();
        
        return response()->json([
            'status' => true,
            'data' => ActivityResource::collection($activities)
        ]);
    }

    /**
     * Get recommended activities
     */
    public function getRecommended()
    {
        $activities = Activity::recommended()
                             ->orderBy('rating', 'desc')
                             ->take(10)
                             ->get();
        
        return response()->json([
            'status' => true,
            'data' => ActivityResource::collection($activities)
        ]);
    }

    /**
     * Get activities by location
     */
    public function getByLocation($location)
    {
        $activities = Activity::byLocation($location)
                             ->orderBy('rating', 'desc')
                             ->orderBy('views', 'desc')
                             ->get();
        
        return response()->json([
            'status' => true,
            'data' => ActivityResource::collection($activities)
        ]);
    }

    /**
     * Get activities by category
     */
    public function getByCategory($category)
    {
        $activities = Activity::where('category', $category)
                             ->orderBy('rating', 'desc')
                             ->orderBy('views', 'desc')
                             ->get();
        
        return response()->json([
            'status' => true,
            'data' => ActivityResource::collection($activities)
        ]);
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        $categories = Activity::distinct('category')
                             ->pluck('category')
                             ->filter()
                             ->values();
        
        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }
}
