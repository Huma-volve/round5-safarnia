<?php

namespace App\Http\Controllers\Api;

use Stripe\Stripe;
use Stripe\Customer;
use App\Models\Payment;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();

        $secretKey = config('services.stripe.secret');
        if (empty($secretKey)) {
            return ApiResponse::sendResponse(500, 'Stripe secret key is not configured');
        }
        Stripe::setApiKey($secretKey);

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

        // authorize ownership
        if ((int)$booking->user_id !== (int)$user->id) {
            return ApiResponse::sendResponse(403, 'Unauthorized action');
        }

        // amount fallback for models that do not store total_price
        $amount = $booking->total_price ?? 0;
        if ($amount <= 0) {
            if ($bookingType === 'room') {
                $room = \App\Models\Room::find($booking->room_id);
                if ($room) {
                    $nights = max(1, (new \DateTime($booking->check_in_date))->diff(new \DateTime($booking->check_out_date))->days);
                    $amount = (float) ($room->price * $nights);
                }
            }
        }
        if ($amount <= 0) {
            return ApiResponse::sendResponse(400, 'Invalid amount for checkout');
        }

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

    $secretKey = config('services.stripe.secret');
    if (empty($secretKey)) {
        return ApiResponse::sendResponse(500, 'Stripe secret key is not configured');
    }
    Stripe::setApiKey($secretKey);

    // Validate PaymentMethod format early (optional but helpful)
    $pmId = $request->payment_method_id;
    if (strpos($pmId, 'pm_') !== 0) {
        return ApiResponse::sendResponse(422, 'Invalid payment_method_id format');
    }

    try {
        // Ensure the PaymentMethod exists and is attached to this customer
        $paymentMethod = PaymentMethod::retrieve($pmId);
        if (!$paymentMethod) {
            return ApiResponse::sendResponse(400, 'No such payment_method');
        }
        if ($paymentMethod->customer !== $user->stripe_customer_id) {
            // Attach to the current user
            PaymentMethod::retrieve($pmId)->attach(['customer' => $user->stripe_customer_id]);
        }

        // Update the PaymentIntent and confirm
        $pi = PaymentIntent::update($payment->stripe_payment_intent_id, [
            'payment_method' => $pmId,
            'customer' => $user->stripe_customer_id,
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
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return ApiResponse::sendResponse(400, $e->getError()->message ?? $e->getMessage());
    }
}






    

}
