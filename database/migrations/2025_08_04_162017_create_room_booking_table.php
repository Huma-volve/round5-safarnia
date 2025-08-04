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

        Schema::create('room_booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('room_id')->references('id')->on('rooms');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->string('status');
            $table->unsignedInteger('adults_count');
            $table->unsignedInteger('children_count');
            $table->unsignedInteger('infants_count');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_booking');
    }
};
