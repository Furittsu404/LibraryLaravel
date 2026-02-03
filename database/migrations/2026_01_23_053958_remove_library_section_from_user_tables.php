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
        // Remove library_section from users table
        if (Schema::hasColumn('users', 'library_section')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['library_section']); // Drop index first
                $table->dropColumn('library_section');
            });
        }

        // Remove library_section from users_archive table
        if (Schema::hasColumn('users_archive', 'library_section')) {
            Schema::table('users_archive', function (Blueprint $table) {
                $table->dropIndex(['library_section']); // Drop index first
                $table->dropColumn('library_section');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add library_section to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('library_section', 50)->default('entrance')->after('account_status');
            $table->index('library_section');
        });

        // Re-add library_section to users_archive table
        Schema::table('users_archive', function (Blueprint $table) {
            $table->string('library_section', 50)->default('entrance')->after('account_status');
            $table->index('library_section');
        });
    }
};
