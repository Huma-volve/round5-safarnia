<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('tour_availability_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id')->comment('FK');
            $table->dateTime('start_time');
            $table->integer('available_seats');
                $table->timestamps();

            $table->dateTime('end_time');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_availability_slots');
    }
};