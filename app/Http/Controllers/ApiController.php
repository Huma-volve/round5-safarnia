<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Category;
use App\Models\Tour;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Get all categories with basic information
     */
    public function all()
    {
        $categories = Category::all();
        return ApiResource::collection($categories);
    }

    /**
     * Get all categories with tours count
     */
    public function categoriesWithCount()
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

    /**
     * Get categories with their recommended tours
     */
    public function categoriesWithRecommendedTours()
    {
        $categories = Category::with(['tours' => function($query) {
            $query->where('is_recommended', true)->take(3);
        }])->get();

        return response()->json([
            'status' => true,
            'data' => $categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'description' => $category->description,
                    'image' => asset('storage/' . $category->image),
                    'recommended_tours' => $category->tours->map(function($tour) {
                        return [
                            'id' => $tour->id,
                            'title' => $tour->title,
                            'price' => $tour->price,
                            'rating' => $tour->rating,
                            'image' => asset('storage/' . $tour->image),
                            'location' => $tour->location,
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * Get home page data (categories + recommended tours)
     */
    public function homePage()
    {
        $categories = Category::withCount('tours')->get();
        $recommendedTours = Tour::where('is_recommended', true)
                               ->with(['category', 'availabilitySlots'])
                               ->take(6)
                               ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'categories' => $categories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'title' => $category->title,
                        'description' => $category->description,
                        'image' => asset('storage/' . $category->image),
                        'tours_count' => $category->tours_count
                    ];
                }),
                'recommended_tours' => $recommendedTours->map(function($tour) {
                    return [
                        'id' => $tour->id,
                        'title' => $tour->title,
                        'price' => $tour->price,
                        'rating' => $tour->rating,
                        'image' => asset('public/storage/' . $tour->image),
                        'location' => $tour->location,
                        'category' => [
                            'id' => $tour->category->id ?? null,
                            'title' => $tour->category->title ?? null,
                        ],
                        'available_slots_count' => $tour->availabilitySlots->where('available_seats', '>', 0)->count(),
                    ];
                })
            ]
        ]);
    }
}
