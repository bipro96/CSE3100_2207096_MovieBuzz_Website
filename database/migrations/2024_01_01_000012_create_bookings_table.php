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
           
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('show_id')->constrained();
            $table->unsignedInteger('seat_count');
            $table->decimal('total_amount', 12, 2);
        
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
