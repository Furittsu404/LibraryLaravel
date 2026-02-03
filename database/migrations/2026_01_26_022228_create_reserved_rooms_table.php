<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('room_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->unsignedInteger('user_id')->constrained('users', 'id')->onDelete('cascade');
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('purpose');
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->timestamps();

            // Prevent overlapping reservations
            $table->unique(['room_id', 'reservation_date', 'start_time'], 'unique_room_time_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_reservations');
    }
};