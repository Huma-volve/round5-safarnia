<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddReviewRequest as AddReviewRequest;
use App\Services\HotelReviewService;

class HotelReviewController extends Controller
{
    public function __construct(
        protected HotelReviewService $ReviewService,
    ) {}
// add review for hotel by room_id
    public function store(AddReviewRequest $request)
    {
        $data = $request->validated();
        return $this->ReviewService->addReview($data);
    }
    // get last 20 review for hotel by room_id
    public function getReviewsForHotel($roomId)
    {
        return $this->ReviewService->getReviewsForHotel($roomId);
    }
}
