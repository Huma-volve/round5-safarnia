<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Hotel;
use App\Models\HotelReview;
use App\Models\Room;
use App\Models\RoomAvailability;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدمين
        $users = User::factory()->count(5)->create();

        // إنشاء فنادق، غرف، صور وتوفر
        Hotel::factory()
            ->count(5)
            ->create([
            'category_id' => CategorySeeder::$hotelCategoryId
        ])
            ->each(function ($hotel) use ($users) {
                // إضافة صورة للفندق
                $hotel->images()->create([
                    'image_path' => 'images/image1.jpg'
                ]);

                // تقييمات
                HotelReview::factory()->count(3)->create([
                    'hotel_id' => $hotel->id,
                    'user_id' => $users->random()->id,
                ]);

                // إنشاء غرف
                $rooms = Room::factory()->count(3)->create([
                    'hotel_id' => $hotel->id
                ]);

                foreach ($rooms as $room) {
                    // صورة لكل غرفة
                    $room->images()->create([
                        'image_path' => 'images/image2.jpg'
                    ]);

                    // توفّر للغرفة
                    RoomAvailability::factory()->count(2)->create([
                        'room_id' => $room->id
                    ]);
                }
            });
    }
}
