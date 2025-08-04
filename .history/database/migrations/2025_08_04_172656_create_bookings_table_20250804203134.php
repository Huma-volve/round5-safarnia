<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('CASCADE');
            $table->foreignId('car_id')->constrained('cars')->onDelete('CASCADE');
            $table->dateTime('pickup_date');
            $table->dateTime('return_date');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->enum('status', ['confirmed', 'ongoing', 'completed', 'canceled'])->default('confirmed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
