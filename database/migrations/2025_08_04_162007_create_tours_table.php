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

        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories'); // ✅ lowercase table name
            $table->string('title', 255);
            $table->string('location');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->string('image', 255);
            $table->integer('views')->default(0);
            $table->boolean('is_recommended')->default(false);
            $table->float('rating')->default(0); // ✅ تم حذف ->after
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
