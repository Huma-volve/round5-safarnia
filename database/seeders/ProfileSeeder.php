<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'osama@example.com'],
            [
                'name' => 'Osama Al-Jaridi',
                'phone' => '01012345678',
                'image' => 'profile/osama.jpg', // صورة المستخدم
                'password' => bcrypt('12345678')
            ]
        );
    }
}
