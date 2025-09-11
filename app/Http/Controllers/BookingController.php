<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class BookingController extends Controller
{
    /**
     * Store a new car booking
     */
    public function store(Request $request)
    {
        // Step 1: Validate the input
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        // Step 2: Find the car
        $car = Car::findOrFail($request->car_id);

        // Step 3: Check if car is available (no overlapping bookings)
        $overlappingBooking = Booking::where('car_id', $car->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('pickup_date', [$request->pickup_date, $request->return_date])
                    ->orWhereBetween('return_date', [$request->pickup_date, $request->return_date]);
            })
            ->exists();

        if ($overlappingBooking) {
            return response()->json([
                'message' => 'Sorry, this car is not available for the selected dates.'
            ], 409); // 409 Conflict
        }

        // Step 4: Calculate rental days and total price
        $pickup = new \DateTime($request->pickup_date);
        $return = new \DateTime($request->return_date);
        $days = $pickup->diff($return)->days;

        // Minimum 1 day
        if ($days === 0) {
            $days = 1;
        }

        $totalPrice = $car->daily_rate * $days;
        $amountInCents = (int) ($totalPrice * 100); // Stripe uses cents

        try {
            // Set API key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create charge
            $charge = Charge::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                //'source' => $request->payment_method_id, // from frontend (e.g., Stripe Elements)
                'source' => 'tok_visa',
                'description' => "Car rental: {$car->model}",
            ]);

            // ✅ Payment succeeded
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'car_id' => $car->id,
                'pickup_date' => $request->pickup_date,
                'return_date' => $request->return_date,
                'total_price' => $totalPrice,
                'status' => 'confirmed',
                'payment_id' => $charge->id, // save Stripe charge ID
            ]);

            // Step 5: Return success response
            return response()->json([
                'message' => 'Car rented successfully!',
                'booking' => $booking->load('car.category'), // include car + category
                'total_price' => number_format($totalPrice, 2),
                'rental_days' => $days,
                'payment_status' => 'paid'
            ], 201); // 201 = Created

        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'message' => 'Payment failed: ' . $e->getError()->message
            ], 402);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get logged-in user's bookings
     */
    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('car.category')
            ->orderBy('pickup_date', 'desc')
            ->get();

        return response()->json($bookings);
    }
}
