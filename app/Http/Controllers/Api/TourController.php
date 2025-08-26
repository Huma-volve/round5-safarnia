<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    /**
     * Display a listing of tours with search and filters
     */
    public function index(Request $request)
    {
        $query = Tour::with(['category', 'availabilitySlots']);

        // Search by keyword
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by rating
        if ($request->has('min_rating') && $request->min_rating) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Filter by location (near me - simplified version)
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            case 'views':
                $query->orderBy('views', $sortOrder);
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tours = $query->paginate($perPage);

        return TourResource::collection($tours);
    }

    /**
     * Display the specified tour
     */
    public function show(Tour $tour)
    {
        // Increment views
        $tour->increment('views');
        
        // Load comprehensive tour data
        $tour->load([
            'category',
            'availabilitySlots' => function($query) {
                $query->orderBy('start_time', 'asc');
            },
            'images',
            'bookings' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'topActivities',
            'popularActivities',
            'recommendedActivities',
            'relatedActivities'
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Tour details retrieved successfully',
            'data' => new TourResource($tour)
        ]);
    }

    /**
     * Get tours by category
     */
    public function getByCategory(Category $category)
    {
        $tours = $category->tours()->with(['availabilitySlots'])->get();
        return TourResource::collection($tours);
    }

    /**
     * Get top rated tours
     */
    public function getTopRated()
    {
        $tours = Tour::where('rating', '>=', 4.0)
                     ->orderBy('rating', 'desc')
                     ->take(10)
                     ->get();
        
        return TourResource::collection($tours);
    }

    /**
     * Get most viewed tours
     */
    public function getMostViewed()
    {
        $tours = Tour::orderBy('views', 'desc')
                     ->take(10)
                     ->get();
        
        return TourResource::collection($tours);
    }

    /**
     * Get tours with available slots
     */
    public function getAvailableTours()
    {
        $tours = Tour::whereHas('availabilitySlots', function($query) {
            $query->where('available_seats', '>', 0);
        })->with(['availabilitySlots'])->get();
        
        return TourResource::collection($tours);
    }

    /**
     * Get all categories with their tours count
     */
    public function getCategoriesWithCount()
    {
        $categories = Category::withCount('tours')->get();
        
        return response()->json([
            'status' => true,
            'data' => $categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'description' => $category->description,
                    'image' => asset('storage/' . $category->image),
                    'tours_count' => $category->tours_count
                ];
            })
        ]);
    }
}
