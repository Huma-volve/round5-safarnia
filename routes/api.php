<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HotelReviewController;
use App\Http\Controllers\Api\RoomBookingController;
use Symfony\Component\Process\Exception\ProcessFailedException;

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


Route::post('/hotel/review', [HotelReviewController::class, 'store'])->middleware('auth:sanctum');
Route::controller(RoomBookingController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/booking/room', 'createRoomBooking');
        Route::get('/my/room/bookings', 'getUserRoomBookings');
    });
