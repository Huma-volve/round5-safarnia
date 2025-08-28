<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Hotel;
use App\Models\Tour;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('key'); // الكلمة اللي بدنا نبحث عنها

        // إذا فيه query نعمل بحث LIKE، إذا فاضي نرجع كل اللوكيشن
        $tourLocations = Tour::when($query, function ($q) use ($query) {
            $q->where('location', 'like', "%{$query}%");
        })->pluck('location');

        return ApiResponse::sendResponse(200, 'Locations retrieved successfully', $tourLocations);
    }
}
