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
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->integer('participant_count')->nullable()->after('purpose');
            $table->json('participant_ids')->nullable()->after('participant_count');
            $table->json('participant_names')->nullable()->after('participant_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->dropColumn('participant_ids');
            $table->dropColumn('participant_count');
            $table->dropColumn('participant_names');
        });
    }
};
