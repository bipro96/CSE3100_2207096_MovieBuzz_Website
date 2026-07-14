<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cinema_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->date('show_date');
            $table->time('show_time');
            $table->dateTime('starts_at'); // computed show_date + show_time, used for overlap & sorting
            $table->dateTime('ends_at');   // starts_at + movie runtime
            $table->string('language')->nullable();
            $table->string('format')->default('2D'); // 2D, 3D, IMAX...
            $table->decimal('ticket_price', 10, 2);
            $table->decimal('premium_price', 10, 2)->nullable();
            $table->decimal('vip_price', 10, 2)->nullable();
            $table->unsignedInteger('total_seats')->default(0);
            $table->unsignedInteger('available_seats')->default(0);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['hall_id', 'starts_at', 'ends_at']);
            $table->index(['movie_id', 'show_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shows');
    }
};
