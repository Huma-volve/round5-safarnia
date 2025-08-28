<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function addToFavorite($tourId)
    {
        $userId = Auth::user()->id;

        // التحقق من وجود التور
        $tourExists = DB::table('tours')->where('id', $tourId)->exists();
        if (!$tourExists) {
            return ApiResponse::sendResponse(404, 'Tour not found');
        }

        // التحقق إذا موجود بالفعل في المفضلة
        $exists = DB::table('favorites')
            ->where('user_id', $userId)
            ->where('tour_id', $tourId)
            ->exists();

        if ($exists) {
            return ApiResponse::sendResponse(400, 'This tour is already in your favorites');
        }

        // إضافة التور للمفضلة
        DB::table('favorites')->insert([
            'user_id' => $userId,
            'tour_id' => $tourId
        ]);

        return ApiResponse::sendResponse(200, 'Tour added to your favorite successfully');
    }

    public function removeFromFavorite($tourId)
    {
        $userId = Auth::user()->id;

        // التحقق من وجود التور
        $tourExists = DB::table('tours')->where('id', $tourId)->exists();
        if (!$tourExists) {
            return ApiResponse::sendResponse(404, 'Tour not found');
        }

        // التحقق إذا موجود في المفضلة
        $favoriteExists = DB::table('favorites')
            ->where('user_id', $userId)
            ->where('tour_id', $tourId)
            ->exists();

        if (!$favoriteExists) {
            return ApiResponse::sendResponse(400, 'This tour is already removed from your favorites');
        }

        // حذف التور من المفضلة
        DB::table('favorites')
            ->where('user_id', $userId)
            ->where('tour_id', $tourId)
            ->delete();

        return ApiResponse::sendResponse(200, 'Tour removed from your favorite successfully');
    }
}
