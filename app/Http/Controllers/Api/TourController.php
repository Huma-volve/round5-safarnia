<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
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
            $query->where(function ($q) use ($request) {
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

        // Filter by duration
        if ($request->has('min_duration') && $request->min_duration) {
            $query->where('duration_hours', '>=', $request->min_duration);
        }
        if ($request->has('max_duration') && $request->max_duration) {
            $query->where('duration_hours', '<=', $request->max_duration);
        }

        // Filter by highlights
        if ($request->has('highlights') && $request->highlights) {
            $highlights = explode(',', $request->highlights);
            $query->where(function ($q) use ($highlights) {
                foreach ($highlights as $highlight) {
                    $q->whereJsonContains('highlights', trim($highlight));
                }
            });
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
            case 'duration':
                $query->orderBy('duration_hours', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tours = $query->paginate($perPage);
        // أضف is_favorite
        $tours->getCollection()->transform(function ($tour) {
            $tour->is_favorite = DB::table('favorites')
                ->where('tour_id', $tour->id)
                ->exists();
            return $tour;
        });
        return TourResource::collection($tours);
    }

    /**
     * Display the specified tour
     */
    public function show($id)
    {
        $tour = Tour::with(['category', 'availabilitySlots'])->find($id);
        if (!$tour) {
            return ApiResponse::sendResponse(404, 'Tour not found');
        }

        $data = [
            'id' => $tour->id,
            'title' => $tour->title,
            'location' => $tour->location,
            'description' => $tour->description,
            'price' => $tour->price,
            'image' => $tour->image,
            'slots' => $tour->availabilitySlots->map(function ($slot) {
                return [
                    'slot_id' => $slot->id,
                    'max_seats' => $slot->max_seats,
                ];
            }),
            'duration' => $tour->duration_hours,
            'highlights' => $tour->highlights,
            'guide'=>$tour->guide,
            'transportation'=>$tour->transportation,
        ];
        return ApiResponse::sendResponse(200, 'Tour details retrieved successfully',$data);
    }

    /**
     * Get tours by category
     */
    public function getByCategory(Category $category)
    {
        $tours = $category->tours()->with([
            'availabilitySlots' => function ($query) {
                $query->orderBy('start_time', 'asc');
            },
            'images'
        ])->get();

        return response()->json([
            'status' => true,
            'message' => 'Tours for category: ' . $category->title,
            'data' => TourResource::collection($tours),
            'category_info' => [
                'id' => $category->id,
                'title' => $category->title,
                'description' => $category->description,
                'image' => asset('storage/' . $category->image),
                'tours_count' => $tours->count()
            ]
        ]);
    }

    /**
     * Get top rated tours
     */
    public function getTopRated()
    {
        $tours = Tour::where('rating', '>=', 4.0)
            ->with([
                'category',
                'availabilitySlots' => function ($query) {
                    $query->where('available_seats', '>', 0)
                        ->orderBy('start_time', 'asc');
                },
                'images'
            ])
            ->orderBy('rating', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Top rated tours retrieved successfully',
            'data' => TourResource::collection($tours),
            'stats' => [
                'total_tours' => $tours->count(),
                'avg_rating' => $tours->avg('rating'),
                'min_rating' => $tours->min('rating'),
                'max_rating' => $tours->max('rating')
            ]
        ])
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
        $tours = Tour::with([
            'category',
            'availabilitySlots' => function ($query) {
                $query->where('available_seats', '>', 0)
                    ->orderBy('start_time', 'asc');
            },
            'images'
        ])
            ->orderBy('views', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Most viewed tours retrieved successfully',
            'data' => TourResource::collection($tours),
            'stats' => [
                'total_tours' => $tours->count(),
                'total_views' => $tours->sum('views'),
                'avg_views' => $tours->avg('views'),
                'max_views' => $tours->max('views')
            ]
        ]);
        $tours = Tour::orderBy('views', 'desc')
            ->take(10)
            ->get();

        return TourResource::collection($tours);
    }

    /**
     * Get tours with available slots and search functionality
     */
    public function getAvailableTours(Request $request)
    {
        $query = Tour::whereHas('availabilitySlots', function ($query) {
            $query->where('available_seats', '>', 0);
        })->with([
            'category',
            'availabilitySlots' => function ($query) {
                $query->where('available_seats', '>', 0)
                    ->orderBy('start_time', 'asc');
            },
            'images'
        ]);

        // Search by keyword
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
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

        // Filter by duration
        if ($request->has('min_duration') && $request->min_duration) {
            $query->where('duration_hours', '>=', $request->min_duration);
        }
        if ($request->has('max_duration') && $request->max_duration) {
            $query->where('duration_hours', '<=', $request->max_duration);
        }

        // Filter by highlights
        if ($request->has('highlights') && $request->highlights) {
            $highlights = is_array($request->highlights) ? $request->highlights : explode(',', $request->highlights);
            $query->where(function ($q) use ($highlights) {
                foreach ($highlights as $highlight) {
                    $q->whereJsonContains('highlights', trim($highlight));
                }
            });
        }

        // Filter by location
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
            case 'duration':
                $query->orderBy('duration_hours', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tours = $query->paginate($perPage);

        // Add is_favorite
        $tours->getCollection()->transform(function ($tour) {
            $tour->is_favorite = DB::table('favorites')
                ->where('tour_id', $tour->id)
                ->exists();
            return $tour;
        });

        return response()->json([
            'status' => true,
            'message' => 'Available tours retrieved successfully',
            'data' => TourResource::collection($tours),
            'availability_stats' => [
                'total_tours' => $tours->total(),
                'current_page' => $tours->currentPage(),
                'per_page' => $tours->perPage(),
                'last_page' => $tours->lastPage(),
                'from' => $tours->firstItem(),
                'to' => $tours->lastItem(),
            ]
        ]);
    }

    /**
     * Get all categories with their tours count
     */
    public function getCategoriesWithCount()
    {
        $categories = Category::withCount('tours')->get();

        return response()->json([
            'status' => true,
            'data' => $categories->map(function ($category) {
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
