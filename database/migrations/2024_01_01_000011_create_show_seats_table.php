<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('show_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hall_seat_id')->constrained()->cascadeOnDelete();
            $table->string('seat_code', 10);
            $table->enum('seat_type', ['regular', 'premium', 'vip', 'disabled', 'unavailable']);
            $table->decimal('price', 10, 2);
            $table->enum('status', ['available', 'locked', 'booked', 'unavailable'])->default('available');
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->unique(['show_id', 'seat_code']);
            $table->index(['show_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('show_seats');
    }
};
