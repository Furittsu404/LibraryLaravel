<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * IMPORTANT: Rename this file to include a proper timestamp prefix before running.
     * Example: 2026_01_20_120000_add_indexes_for_reports_performance.php
     *
     * These indexes will significantly improve query performance for the reports page,
     * especially when dealing with 10,000+ records.
     */
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Index for login_time range queries (most important)
            // This helps with the WHERE login_time BETWEEN queries
            $table->index('login_time', 'idx_attendance_login_time');

            // Index for user_id (helps with joins)
            if (!Schema::hasColumn('attendance', 'user_id')) {
                // Adjust this if your column name is different
                $table->index('user_id', 'idx_attendance_user_id');
            }

            // Composite index for login_time and user_id together
            // This is extremely helpful for queries that filter by time and join on user_id
            $table->index(['login_time', 'user_id'], 'idx_attendance_login_time_user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index for account_status (used in WHERE clause)
            $table->index('account_status', 'idx_users_account_status');

            // Index for gender (used in WHERE and conditional aggregation)
            $table->index('gender', 'idx_users_gender');

            // Index for user_type (used in WHERE and conditional aggregation)
            $table->index('user_type', 'idx_users_user_type');

            // Index for course (used in WHERE IN clause)
            $table->index('course', 'idx_users_course');

            // Composite index for common filter combinations
            $table->index(['account_status', 'gender'], 'idx_users_status_gender');
            $table->index(['account_status', 'user_type'], 'idx_users_status_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_login_time');
            $table->dropIndex('idx_attendance_user_id');
            $table->dropIndex('idx_attendance_login_time_user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_account_status');
            $table->dropIndex('idx_users_gender');
            $table->dropIndex('idx_users_user_type');
            $table->dropIndex('idx_users_course');
            $table->dropIndex('idx_users_status_gender');
            $table->dropIndex('idx_users_status_type');
        });
    }
};
