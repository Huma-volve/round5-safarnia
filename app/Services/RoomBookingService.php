<?php

namespace App\Services;

use App\Enums\RoomBookingStatus;
use App\Helpers\ApiResponse;
use App\Models\RoomAvailability;
use App\Models\RoomBooking;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomBookingService
{
    public function MakeRoomBooking(array $data)
    {
        DB::beginTransaction();

        try {
            // التأكد من عدم وجود حجز متداخل على نفس الغرفة
            $availabilityBooking = RoomBooking::where('room_id', $data['room_id'])
                ->where('status', '!=', RoomBookingStatus::Cancelled->value)
                ->where(function ($query) use ($data) {
                    $query->whereBetween('check_in_date', [$data['check_in_date'], $data['check_out_date']])
                        ->orWhereBetween('check_out_date', [$data['check_in_date'], $data['check_out_date']])
                        ->orWhere(function ($q) use ($data) {
                            $q->where('check_in_date', '<=', $data['check_in_date'])
                                ->where('check_out_date', '>=', $data['check_out_date']);
                        });
                })
                ->exists();
            if ($availabilityBooking) {
                return ApiResponse::sendResponse(200,"Room is already booked for the selected dates.");
            }
            // التحقق من توفر الغرفة في المدى الكامل
            $availability = RoomAvailability::where('room_id', $data['room_id'])
                ->where('available_from', '<=', $data['check_in_date'])
                ->where('available_to', '>=', $data['check_out_date'])
                ->first();

            if (!$availability) {
                return ApiResponse::sendResponse(200,"Room is not available in the selected date range.");
            }

            // إنشاء الحجز
            $booking = RoomBooking::create([
                'user_id' => Auth::id(),
                'room_id' => $data['room_id'],
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'adults_count' => $data['adults_count'],
                'children_count' => $data['children_count'] ?? 0,
                'infants_count' => $data['infants_count'] ?? 0,
            ]);
            // حذف الاتاحة الأصلية
            $availability->delete();

            // إنشاء اتاحة قبل الحجز (إذا فيه فرق)
            if (strtotime($availability->available_from) < strtotime($data['check_in_date'])) {
                RoomAvailability::create([
                    'room_id' => $data['room_id'],
                    'available_from' => $availability->available_from,
                    'available_to' => date('Y-m-d', strtotime($data['check_in_date'] . ' -1 day')),
                ]);
            }

            // إنشاء اتاحة بعد الحجز (إذا فيه فرق)
            if (strtotime($availability->available_to) > strtotime($data['check_out_date'])) {
                RoomAvailability::create([
                    'room_id' => $data['room_id'],
                    'available_from' => date('Y-m-d', strtotime($data['check_out_date'] . ' +1 day')),
                    'available_to' => $availability->available_to,
                ]);
            }

            DB::commit();
            return ApiResponse::sendResponse(201, 'The room has been booked successfully', $booking);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponse(500, $e->getMessage());
        }
    }
    public function MyRoomBooking()
    {
        $userId = Auth::user()->id;
        $booking = RoomBooking::where('user_id', $userId)->get();
        return ApiResponse::sendResponse(200, 'your Room Booking retrieved successfully', $booking);
    }

    public function cancelMyRoomBooking($bookingId)
    {
        $userId = Auth::id();

        $booking = RoomBooking::where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            return ApiResponse::sendResponse(404, 'Booking not found');
        }
        // إذا الحجز بالفعل ملغى
        if ($booking->status === RoomBookingStatus::Cancelled->value) {
            return ApiResponse::sendResponse(409, 'Booking is already cancelled.');
        }
        // اسمح فقط بالإلغاء إذا كانت الحالة pending
        if ($booking->status !== RoomBookingStatus::Pending->value) {
            return ApiResponse::sendResponse(409, 'Only pending bookings can be cancelled.');
        }

        $booking->status = RoomBookingStatus::Cancelled->value;
        $booking->save();

        // بعد الإلغاء رجّع الغرفة متاحة
        RoomAvailability::create([
            'room_id' => $booking->room_id,
            'available_from' => $booking->check_in_date,
            'available_to' => $booking->check_out_date,
        ]);

        return ApiResponse::sendResponse(200, 'Booking has been cancelled successfully', $booking);
    }

    public function updateRoomBooking(int $bookingId, array $data)
    {
        DB::beginTransaction();

        try {
            $booking = RoomBooking::findOrFail($bookingId);
            // ما بيقدر يعدل على الحجز اذا الحجز حالته ملغية
            if ($booking->status === RoomBookingStatus::Cancelled->value) {
                return ApiResponse::sendResponse(409, 'This booking has already been cancelled and cannot be modified.');
            }
            // تواريخ الحجز القديمة في حال ما عدل على التواريخ
            $oldRoomId = $booking->room_id;
            $oldCheckIn = $booking->check_in_date;
            $oldCheckOut = $booking->check_out_date;
            // التواريخ الجديدة
            $roomId = $data['room_id'] ?? $oldRoomId;
            $checkInDate = $data['check_in_date'] ?? $oldCheckIn;
            $checkOutDate = $data['check_out_date'] ?? $oldCheckOut;

            // 1. أفرغ فترة الحجز القديم من التوفر بإعادة بناء التوفر القديم (بحذف أو تعديل التوفر الحالي)
            // يمكنك حذف التوفرات المتداخلة مع فترة الحجز القديم أولاً
            RoomAvailability::where('room_id', $oldRoomId)
                ->where(function ($q) use ($oldCheckIn, $oldCheckOut) {
                    $q->whereBetween('available_from', [$oldCheckIn, $oldCheckOut])
                        ->orWhereBetween('available_to', [$oldCheckIn, $oldCheckOut])
                        ->orWhere(function ($query) use ($oldCheckIn, $oldCheckOut) {
                            $query->where('available_from', '<=', $oldCheckIn)
                                ->where('available_to', '>=', $oldCheckOut);
                        });
                })
                ->delete();
            // اجعل الغرفة متاحة في فترة الحجز القديم الذي تم تعديلها
            RoomAvailability::create([
                'room_id' => $oldRoomId,
                'available_from' => $oldCheckIn,
                'available_to' => $oldCheckOut,
            ]);

            //  تحقق من عدم تداخل الحجز الجديد مع حجوزات أخرى ما عدا هذا الحجز نفسه
            $availabilityBooking = RoomBooking::where('room_id', $roomId)
                ->where('status', '!=', RoomBookingStatus::Cancelled->value)
                ->where('id', '!=', $bookingId)
                ->where(function ($query) use ($checkInDate, $checkOutDate) {
                    $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                        ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                        ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                            $q->where('check_in_date', '<=', $checkInDate)
                                ->where('check_out_date', '>=', $checkOutDate);
                        });
                })
                ->exists();

            if ($availabilityBooking) {
                return ApiResponse::sendResponse(200,"Room is already booked for the selected dates.");
            }
            // تحقق من توافر الغرفة في الفترة الجديدة المدخلة
            $availability = RoomAvailability::where('room_id', $roomId)
                ->where('available_from', '<=', $checkInDate)
                ->where('available_to', '>=', $checkOutDate)
                ->first();

            if (!$availability) {
                return ApiResponse::sendResponse(200,"Room is not available in the selected date range.");
            }

            //  بعد التأكد من التوفر، نحذف التوفر الحالي الذي يغطي الفترة الجديدة (لنبني التوفر من جديد)
            RoomAvailability::where('room_id', $roomId)
                ->where('available_from', '<=', $checkInDate)
                ->where('available_to', '>=', $checkOutDate)
                ->delete();

            //  إنشاء اتاحة قبل الحجز (إذا فيه فرق)
            if (strtotime($availability->available_from) < strtotime($checkInDate)) {
                RoomAvailability::create([
                    'room_id' => $roomId,
                    'available_from' => $availability->available_from,
                    'available_to' => date('Y-m-d', strtotime($checkInDate . ' -1 day')),
                ]);
            }
            // بمعنى اذا كان الحجز لا يغطي فترة اتاحة الغرفة بشكل كامل نقسم فترة الاتاحة
            // . إنشاء اتاحة بعد الحجز (إذا فيه فرق)
            if (strtotime($availability->available_to) > strtotime($checkOutDate)) {
                RoomAvailability::create([
                    'room_id' => $roomId,
                    'available_from' => date('Y-m-d', strtotime($checkOutDate . ' +1 day')),
                    'available_to' => $availability->available_to,
                ]);
            }

            //  تحديث بيانات الحجز
            // اذا ما دخل هدول الحقول حطلهن قيمة افتراضية صفر
            $data['children_count'] = $data['children_count'] ?? 0;
            $data['infants_count'] = $data['infants_count'] ?? 0;
            $fieldsToUpdate = ['room_id', 'check_in_date', 'check_out_date', 'adults_count', 'children_count', 'infants_count'];

            foreach ($fieldsToUpdate as $field) {
                if (array_key_exists($field, $data)) {
                    $booking->$field = $data[$field];
                }
            }
            $booking->save();


            DB::commit();

            return ApiResponse::sendResponse(200, 'Booking updated successfully', $booking);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return ApiResponse::sendResponse(404, 'Booking not found');
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse::sendResponse(500, $e->getMessage());
        }
    }
}
