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
        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'duration_hours')) {
                $table->integer('duration_hours')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('tours', 'max_group_size')) {
                $table->integer('max_group_size')->nullable()->after('duration_hours');
            }
            if (!Schema::hasColumn('tours', 'min_age')) {
                $table->integer('min_age')->nullable()->after('max_group_size');
            }
            if (!Schema::hasColumn('tours', 'difficulty_level')) {
                $table->enum('difficulty_level', ['easy', 'moderate', 'challenging', 'expert'])->nullable()->after('min_age');
            }
            if (!Schema::hasColumn('tours', 'highlights')) {
                $table->json('highlights')->nullable()->after('difficulty_level');
            }
            if (!Schema::hasColumn('tours', 'included_services')) {
                $table->json('included_services')->nullable()->after('highlights');
            }
            if (!Schema::hasColumn('tours', 'excluded_services')) {
                $table->json('excluded_services')->nullable()->after('included_services');
            }
            if (!Schema::hasColumn('tours', 'what_to_bring')) {
                $table->json('what_to_bring')->nullable()->after('excluded_services');
            }
            if (!Schema::hasColumn('tours', 'cancellation_policy')) {
                $table->text('cancellation_policy')->nullable()->after('what_to_bring');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'duration_hours',
                'max_group_size',
                'min_age',
                'difficulty_level',
                'highlights',
                'included_services',
                'excluded_services',
                'what_to_bring',
                'cancellation_policy'
            ]);
        });
    }
};
