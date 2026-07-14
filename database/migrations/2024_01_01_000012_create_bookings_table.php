<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 20)->unique(); // e.g. MB20260705XXXX
            $table->string('booking_code', 12)->unique(); // short code for QR / counter verification
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('show_id')->constrained();
            $table->unsignedInteger('seat_count');
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_method', ['wallet'])->default('wallet');
            $table->enum('status', ['confirmed', 'cancelled', 'completed'])->default('confirmed');
            $table->enum('refund_status', ['none', 'refunded', 'not_eligible'])->default('none');
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
