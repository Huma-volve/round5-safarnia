<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\RecommendedTourController;
use App\Http\Controllers\TourAvailSlotController;
use App\Http\Controllers\TourBookingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HotelReviewController;
use App\Http\Controllers\Api\RoomBookingController;
use App\Http\Controllers\Api\FlightBookingController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\ActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Webhook for deployment
 */
Route::get('/webhook-handler', function () {
    $process = new Process(['/bin/bash', '/home/digital07/round5-safarnia.digital-vision-solutions.com/deploy.sh']);

    try {
        $process->mustRun();
    } catch (ProcessFailedException $exception) {
        return response('Deployment failed: ' . $exception->getMessage(), 500);
    }

    return response('Deployment completed successfully.', 200);
});

/**
 * Authentication Routes
 */
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/otp', 'otp');
    Route::post('/forgot-password', 'forgotPassword');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
        Route::post('/reset-password', 'resetPassword');
        Route::post('/delete-account', 'deleteAccount');
        Route::post('/update-password', 'updatePassword');
    });
});

/**
 * General Data & Home Page
 */
Route::get('allcategory', [ApiController::class, 'all']);
Route::get('categories-with-count', [ApiController::class, 'categoriesWithCount']);
Route::get('categories-with-recommended-tours', [ApiController::class, 'categoriesWithRecommendedTours']);
Route::get('home-page', [ApiController::class, 'homePage']);

/**
 * Recommended Tours
 */
Route::get('recommendedtour', [RecommendedTourController::class, 'recommended']);
Route::get('top-rated-tours', [RecommendedTourController::class, 'topRated']);
Route::get('most-viewed-tours', [RecommendedTourController::class, 'mostViewed']);
Route::get('trending-tours', [RecommendedTourController::class, 'trending']);
Route::get('recommended-tours-by-category/{categoryId}', [RecommendedTourController::class, 'byCategory']);

/**
 * Tours & Search
 */
Route::controller(TourController::class)->group(function () {
    Route::get('/tours', 'index');
    Route::get('/tours/{tour}', 'show');
    Route::get('/tours-by-category/{category}', 'getByCategory');
    Route::get('/top-rated-tours', 'getTopRated');
    Route::get('/most-viewed-tours', 'getMostViewed');
    Route::get('/available-tours', 'getAvailableTours');
    Route::get('/categories-with-tours-count', 'getCategoriesWithCount');

});

/**
 * Activities
 */
Route::controller(ActivityController::class)->group(function () {
    Route::get('/activities', 'index');
    Route::get('/activities/{activity}', 'show');
    Route::get('/top-rated-activities', 'getTopRated');
    Route::get('/popular-activities', 'getPopular');
    Route::get('/recommended-activities', 'getRecommended');
    Route::get('/activities-by-location/{location}', 'getByLocation');
    Route::get('/activities-by-category/{category}', 'getByCategory');
    Route::get('/activity-categories', 'getCategories'); 
});

/**
 * Tour Availability Slots
 */
Route::prefix('tours/{tour}')->group(function () {
    Route::get('slots', [TourAvailSlotController::class, 'index']);
    Route::get('slots-by-date-range', [TourAvailSlotController::class, 'getByDateRange']);
    Route::get('all-slots', [TourAvailSlotController::class, 'getAllSlots']);

    // Admin routes (should be protected with admin middleware)
    Route::post('slots', [TourAvailSlotController::class, 'store']);
    Route::post('bulk-create-slots', [TourAvailSlotController::class, 'bulkCreate']);
});

Route::put('slots/{slot}', [TourAvailSlotController::class, 'update']);
Route::delete('slots/{slot}', [TourAvailSlotController::class, 'destroy']);

/**
 * Tour Bookings
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(TourBookingController::class)->group(function () {
        Route::post('/tour-bookings', 'store');
        Route::get('/tour-bookings/{id}', 'show');
        Route::put('/tour-bookings/{id}', 'update');
        Route::get('/my-tour-bookings', 'myBookings');
        Route::post('/tour-bookings/{id}/cancel', 'cancel');
    });
});

/**
 * Profile Management
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'show');
        Route::post('/profile', 'update');
        Route::post('/profile/delete-account', 'deleteAccount');
        Route::post('/profile/update-password', 'updatePassword');
        Route::get('/profile/booking-history', 'bookingHistory');
    });
});

/**
 * Cars
 */
Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{id}', [CarController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/my', [BookingController::class, 'myBookings']);
});

/**
 * Hotels & Rooms
 */
Route::post('/hotel/review', [HotelReviewController::class, 'store'])->middleware('auth:sanctum');
Route::get('/hotel/review/{roomId}', [HotelReviewController::class, 'getReviewsForHotel']);
Route::get('/hotel/rooms/{hotel_id}', [HotelController::class, 'getRoomsForHotel']);
Route::get('/hotel/rooms', [HotelController::class, 'getRooms']);
Route::get('/room/details/{room_id}', [HotelController::class, 'getRoomDetails']);

Route::get('/hotels',[HotelController::class, 'getHotels']);
Route::get('/hotels/search',[HotelController::class, 'searchHotels']);
Route::get('/rooms/search',[HotelController::class,'searchRooms']);
Route::controller(RoomBookingController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/booking/room', 'createRoomBooking');          // إنشاء حجز
        Route::get('/my/room/bookings', 'getUserRoomBookings');     // عرض حجوزاتي
        Route::put('/booking/room/{bookingId}', 'updateRoomBooking'); // تعديل الحجز
        Route::patch('/booking/room/cancel/{bookingId}', 'cancelRoomBooking'); // إلغاء الحجز
    });

/**
 * Flights
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(FlightController::class)->group(function () {
        Route::get('/flights', 'index');
        Route::get('/flights/{id}', 'show');
    });

    Route::controller(FlightBookingController::class)->group(function () {
        Route::post('/booking/flight', 'store');
        Route::get('/my-bookings/flight', 'myBookingsFlight');
        Route::post('/cancel/flight/{id}', 'destroy');
        Route::post('/update/flight/{id}', 'update');
    });
});
