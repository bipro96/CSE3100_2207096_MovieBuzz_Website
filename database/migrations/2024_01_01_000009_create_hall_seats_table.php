<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->string('row_label', 5); // A, B, C ...
            $table->unsignedInteger('column_number'); // 1, 2, 3 ...
            $table->string('seat_code', 10); // e.g. A1, B12
            $table->enum('seat_type', ['regular', 'premium', 'vip', 'disabled', 'unavailable'])->default('regular');
            $table->timestamps();

            $table->unique(['hall_id', 'seat_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_seats');
    }
};
