<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('show_seat_id')->constrained();
            $table->string('seat_code', 10);
            $table->string('seat_type', 20);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['booking_id', 'show_seat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_seats');
    }
};
