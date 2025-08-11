<?php

namespace App\Http\Controllers;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::with('category')->get();
        return response()->json($cars);
    }

    public function show($id)
    {
        $car = Car::with('category')->find($id);

        if (!$car) {
            return response()->json([
                'message' => 'Car not found.'
            ], 404);
        }

        return response()->json($car);
    }
}
