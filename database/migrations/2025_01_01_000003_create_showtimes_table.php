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
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_movie_id'); // Reference ID from TMDb API
            $table->string('movie_title'); // Cached title to reduce API calls
            $table->string('poster_path')->nullable(); // Cached poster path
            $table->foreignId('studio_id')->constrained()->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time'); // Used to trigger cleaning tasks
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->index(['tmdb_movie_id', 'start_time']);
            $table->index('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
