<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // عرض بيانات البروفايل
    public function show()
    {
        $user = Auth::user();
        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    // تحديث بيانات البروفايل
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validation داخل الكنترولر
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث البيانات
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        // لو فيه صورة جديدة
        if ($request->hasFile('avatar')) {
            // حذف القديمة لو موجودة
            if ($user->image) {
                Storage::delete($user->image);
            }
            $path = $request->file('image')->store('avatars', 'public');
            $user->image = $path;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }
}
