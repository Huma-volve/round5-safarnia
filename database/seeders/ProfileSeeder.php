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
        User::create([
            'name' => 'Osama Al-Jaridi',
            'email' => 'osama@example.com',
            'phone' => '01012345678',
            'image' => 'profile/osama.jpg', // صورة المستخدم
                        
            'password' => bcrypt('12345678')
        ]);
    }
}
