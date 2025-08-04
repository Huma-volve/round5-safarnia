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
        Schema::table('users', function (Blueprint $table) {
               $table->string('image')->after('password')->nullable();
            $table->string('country')->after('image')->nullable();
            $table->string('phone')->after('country')->nullable();
            $table->string('otp')->after('phone')->nullable();
            $table->timestamp('otp_expire_at')->after('otp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
