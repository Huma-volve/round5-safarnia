<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\FlightSeat;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flights = Flight::All();

        foreach ($flights as $flight) {

            for ($i = 1; $i <= 30; $i++) {
                FlightSeat::create([
                    'flight_id' => $flight->id,
                    'seat_number' => $i,
                    'status' => 'available',
                ]);
            }
        }
    }
}
