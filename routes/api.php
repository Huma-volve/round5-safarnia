<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\RecommendedTourController;
use App\Http\Controllers\TourAvailSlotController;
use App\Http\Controllers\Api\TourBookingController;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
use App\Http\Controllers\Api\AuthController;
use Symfony\Component\Process\Exception\ProcessFailedException;

// Route::controller(AuthController::class)->group(function () {
//     Route::post('/register', 'register');
//     Route::post('/login', 'login');
//     Route::post('/logout', 'logout');
// });



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/webhook-handler', function () {
    // Run the deploy script
    $process = new Process(['/bin/bash', '/home/digital07/round5-safarnia.digital-vision-solutions.com/deploy.sh']);

    try {
        $process->mustRun(); // This will throw an exception if the command fails
    } catch (ProcessFailedException $exception) {
        return response('Deployment failed: ' . $exception->getMessage(), 500);
    }

    return response('Deployment completed successfully.', 200);
});


Route::get('allcategory', [ApiController::class, 'all']);


Route::get('recommendedtour', [RecommendedTourController::class, 'recommended']);


Route::prefix('tours/{tour}')->group(function () {
    Route::get('slots', [TourAvailSlotController::class, 'index']);
    Route::post('slots', [TourAvailSlotController::class, 'store']);
});
Route::put('slots/{slot}', [TourAvailSlotController::class, 'update']);
Route::delete('slots/{slot}', [TourAvailSlotController::class, 'destroy']);




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tour-bookings', [TourBookingController::class, 'store']);
    Route::put('/tour-bookings/{id}', [TourBookingController::class, 'update']);
    Route::delete('/tour-bookings/{id}', [TourBookingController::class, 'destroy']);
    Route::get('/my-tour-bookings', [TourBookingController::class, 'myBookings']);
});


//profile page
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
});
