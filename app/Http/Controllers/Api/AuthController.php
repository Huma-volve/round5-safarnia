<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Helpers\Helpers;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Api\loginRequest;
use App\Http\Requests\Api\VerifyRequest;
use App\Http\Resources\Api\UserResource;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\ResendOrForgetRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create($validatedData);
        $data['email'] = $user->email;
        event(new Registered($user));
        return ApiResponse::sendResponse(201,'Otp Sent Successfully', $data);
    }

    public function login(loginRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::where('email', $validatedData['email'])->first();
        if (!$user) {
            return ApiResponse::sendResponse(400, 'Invalid Credentials', null);
        }
        if (!$user->email_verified_at) {
            return ApiResponse::sendResponse(403, 'Account is not verified. Please verify your email before logging in.', null);
        }
        if (auth()->attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            $user = auth()->user();
            $data['token'] = Helpers::createToken($user, 'LoginToken');
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['phone'] = $user->phone;
            $data['country'] = $user->country;
            $data['image'] = $user->image;
            return ApiResponse::sendResponse(200, 'Login successful', $data);
        }
        return ApiResponse::sendResponse(400, 'Invalid Credentials', null);
    }

    public function otp(VerifyRequest $request)
    {
        $request->validated();
        $user = User::where('otp', $request->otp)->where('email', $request->email)->first();

        if ($user) {
            if ($user->otp_expire_at < now()) {
                return ApiResponse::sendResponse(400, 'OTP Expired', null);
            }
            $user->markEmailAsVerified();
            $user->otp = null;
            $user->otp_expire_at = null;
            $user->save();
            $data['token'] = Helpers::createToken($user, 'RegisterToken');
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['phone'] = $user->phone;
            $data['country'] = $user->country;
            $data['image'] = $user->image;

            return ApiResponse::sendResponse(200, 'Email Verified Successfully', $data);
        }
        return ApiResponse::sendResponse(400, 'Invalid OTP', null);
    }

    public function forgotPassword(ResendOrForgetRequest $request)
    {
        $request->validated();
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->sendEmailVerificationNotification();
        }
        return ApiResponse::sendResponse(200, 'If this email exists in our system, an OTP has been sent.', null);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::sendResponse(200, 'User Logged Out Successfully', null);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $request->validated();
        $user = auth()->user();
        if (!$user) {
            return ApiResponse::sendResponse(400, 'Invalid or Expired Token', null);
        }
        $user->update(['password' => $request->password]);
        $user->tokens()->delete();
        return ApiResponse::sendResponse(200, 'Password Reset Successfully', null);
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);
        $user = auth()->user();
        if (Hash::check($request->password, $user->password)) {
            $user->tokens()->delete();
            $user->delete();
            return ApiResponse::sendResponse(200, 'Account Deleted Successfully', null);
        }
        return ApiResponse::sendResponse(400, 'Current Password is Incorrect', null);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $user = auth()->user();
        if (Hash::check($request->password, $user->password)) {
            $user->update(['password' => $request->new_password]);
            return ApiResponse::sendResponse(200, 'Password Updated Successfully', null);
        }
        return ApiResponse::sendResponse(400, 'Current Password is Incorrect', null);
    }
}
