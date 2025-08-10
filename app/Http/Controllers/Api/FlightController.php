<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Models\Flight;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\FlightResource;

class FlightController extends Controller
{
    public function index(Request $request){
         $flights = Flight::with('flightSeats')
        ->when($request->from, fn($q) => $q->where('from', $request->from))
        ->when($request->to, fn($q) => $q->where('to', $request->to))
        ->when($request->date, fn($q) => $q->whereDate('departure_time', $request->date))
        ->get();

        if($flights){
         return ApiResponse::sendResponse(200, 'Flights retrieved successfully', FlightResource::collection($flights));
        }
        return ApiResponse::sendResponse(200, 'No flights found');
    }

    public function show($id){
        $flight = Flight::with('flightSeats')->findOrFail($id);
        if(!$flight){
            return ApiResponse::sendResponse(404, 'Flight not found');
        }
        return ApiResponse::sendResponse(200, 'Flight retrieved successfully', new FlightResource($flight));
    }

}
