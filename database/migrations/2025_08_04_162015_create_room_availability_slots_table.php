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

        Schema::create('room_availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->references('id')->on('rooms');
            $table->date('available_from');
            $table->date('available_to');
            $table->decimal('discount', 5, 2)->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_availability_slots');
    }
};
