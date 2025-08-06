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

    public function store(AddReviewRequest $request)
    {
        $data = $request->validated();
        return $this->ReviewService->addReview($data);
    }
}
