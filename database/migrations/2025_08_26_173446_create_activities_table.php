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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('location');
            $table->string('country');
            $table->string('city');
            $table->float('rating')->default(0);
            $table->json('price_range')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->string('category');
            $table->string('image')->nullable();
            $table->json('highlights')->nullable();
            $table->json('included_services')->nullable();
            $table->json('excluded_services')->nullable();
            $table->json('what_to_bring')->nullable();
            $table->string('best_time_to_visit')->nullable();
            $table->enum('difficulty_level', ['easy', 'moderate', 'challenging', 'expert'])->default('moderate');
            $table->integer('min_age')->nullable();
            $table->integer('max_group_size')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
