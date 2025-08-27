<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTourReviewRequest;
use App\Models\Tour;
use App\Models\TourReview;
use App\Helpers\ApiResponse;

class TourReviewController extends Controller
{
    public function store(StoreTourReviewRequest $request)
    {
        $user = auth()->user();

        // Check if user already reviewed this tour
        $existing = TourReview::where('tour_id', $request->tour_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return ApiResponse::sendResponse(
                400,
                'You have already reviewed this tour.',
                null
            );
        }

        // Create new review
        $review = TourReview::create([
            'tour_id' => $request->tour_id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        // Load user data to return
        $review->load('user:id,name');

        return ApiResponse::sendResponse(
            201,
            'Review submitted successfully.',
            $review
        );
    }

    /**
     * Get all reviews for a tour
     */
    public function index(Tour $tour)
    {
        $reviews = TourReview::with('user:id,name')
            ->where('tour_id', $tour->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'tour_id' => $tour->id,
            'total_reviews' => $reviews->count(),
            'reviews' => $reviews
        ];

        return ApiResponse::sendResponse(
            200,
            'Reviews retrieved successfully.',
            $data
        );
    }
}
