<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display user profile data
     */
    public function show()
    {
        $user = Auth::user()->load(['roomBookings', 'hotelReviews', 'flightBookings']);

        return response()->json([
            'status' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country' => $user->country,
                    'image' => $user->image ? asset('public/storage/' . $user->image) : null,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at?->format('Y-m-d H:i:s'),
                ],
                'stats' => [
                    'total_bookings' => $user->roomBookings->count() + $user->flightBookings->count(),
                    'total_reviews' => $user->hotelReviews->count(),
                    'member_since' => $user->created_at?->diffForHumans(),
                ]
            ]
        ]);
    }

    /**
     * Update user profile data
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'country' => 'sometimes|nullable|string|max:100',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update basic information
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('country')) {
            $user->country = $request->country;
        }

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // Store new image
            $path = $request->file('image')->store('profiles', 'public');
            $user->image = $path;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country' => $user->country,
                    'image' => $user->image ? asset('public/storage/' . $user->image) : null,
                    'updated_at' => $user->updated_at?->format('Y-m-d H:i:s'),
                ]
            ]
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount()
    {
        $user = Auth::user();

        // Delete user's image if exists
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        // Delete user account
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Account deleted successfully'
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully'
        ]);
    }

    /**
     * Get user's booking history
     */
    public function bookingHistory()
    {
        $user = Auth::user();

        $bookings = [
            'room_bookings' => $user->roomBookings()->with(['room.hotel'])->latest()->get(),
            'flight_bookings' => $user->flightBookings()->with(['flight'])->latest()->get(),
        ];

        return response()->json([
            'status' => true,
            'data' => $bookings
        ]);
    }
}
