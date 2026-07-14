<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('original_title')->nullable();
            $table->string('original_language', 10)->nullable();
            $table->text('overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->date('release_date')->nullable();
            $table->unsignedInteger('runtime')->nullable(); // minutes
            $table->string('language')->nullable();
            $table->decimal('popularity', 10, 3)->nullable();
            $table->decimal('vote_average', 4, 2)->nullable();
            $table->unsignedInteger('vote_count')->nullable();
            $table->boolean('adult')->default(false);
            $table->string('status')->nullable(); // Released, Upcoming, etc (TMDb status)
            $table->string('production_countries')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0); // internal, from reviews
            $table->unsignedInteger('rating_count')->default(0);
            $table->enum('listing_status', ['now_showing', 'upcoming', 'archived'])->default('upcoming');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['listing_status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
