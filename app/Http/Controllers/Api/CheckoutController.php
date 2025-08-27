<?php

namespace App\Http\Controllers\Api;

use Stripe\Stripe;
use Stripe\Customer;
use App\Models\Payment;
use Stripe\PaymentIntent;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();

        Stripe::setApiKey(config('services.stripe.secret'));

        // لو معندوش stripe customer نعمله واحد
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name'  => $user->name,
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        // حدد نوع الحجز
        $bookingType = $request->input('booking_type'); // room | car | flight
        $bookingId   = $request->input('booking_id');

        $booking = match ($bookingType) {
            'room'   => \App\Models\RoomBooking::findOrFail($bookingId),
            'car'    => \App\Models\Booking::findOrFail($bookingId),
            'flight' => \App\Models\FlightBooking::findOrFail($bookingId),
            'tour'   => \App\Models\TourBooking::findOrFail($bookingId),
            default  => abort(400, 'Invalid booking type'),
        };

        $amount = $booking->total_price ?? 0;

        // نعمل Intent
        $pi = PaymentIntent::create([
            'amount' => (int) round($amount * 100),
            'currency' => 'usd',
            'customer' => $user->stripe_customer_id,
            'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
            'metadata' => [
                'booking_type' => $bookingType,
                'booking_id'   => $booking->id,
            ],
        ]);

        // نسجل الـ payment عندنا
        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_id' => $booking->id,
            'payable_type' => get_class($booking),
            'amount' => $amount,
            'status' => 'pending',
            'stripe_payment_intent_id' => $pi->id,
        ]);

        return ApiResponse::sendResponse(201 , 'Checkout created' , ['payment_id' => $payment->id , 'client_secret' => $pi->client_secret]);
    }


   public function confirmWithSavedPM(Request $request)
{
    $request->validate([
        'payment_id' => 'required|integer',
        'payment_method_id' => 'required|string',
    ]);

    $user = $request->user();

    $payment = Payment::where('id', $request->payment_id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    Stripe::setApiKey(config('services.stripe.secret'));

    // نحدث الـ PaymentIntent ونأكد
    $pi = PaymentIntent::update($payment->stripe_payment_intent_id, [
        'payment_method' => $request->payment_method_id,
    ]);

      $pi = PaymentIntent::retrieve($payment->stripe_payment_intent_id);
        $pi->confirm();

    if ($pi->status === 'succeeded') {
        $payment->update(['status' => 'succeeded']);

        $booking = $payment->payable;
        if ($booking->user_id !== $user->id) {
            abort(403, 'Unauthorized action');
        }
        $booking->update(['status' => 'confirmed']);

        return ApiResponse::sendResponse(200 , 'Payment confirmed and booking updated');
    }

    return ApiResponse::sendResponse(400 , 'Payment not completed yet');
}






    

}
