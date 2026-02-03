<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create sections table
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('icon', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default sections
        DB::table('sections')->insert([
            ['code' => 'entrance', 'name' => 'Library Entrance', 'icon' => '🚪', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'serials', 'name' => 'Serials & Reference', 'icon' => '📰', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'humanities', 'name' => 'Humanities', 'icon' => '📚', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'multimedia', 'name' => 'Multimedia', 'icon' => '🎬', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'filipiniana', 'name' => 'Filipiniana & Theses', 'icon' => '🇵🇭', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'relegation', 'name' => 'Relegation', 'icon' => '📦', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'science', 'name' => 'Science & Technology', 'icon' => '🔬', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Add section column to users table if it doesn't exist
        if (!Schema::hasColumn('users', 'library_section')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('library_section', 50)->default('entrance')->after('section');
                $table->index('library_section');
            });
        }

        // Add section column to users_archive table if it doesn't exist
        if (Schema::hasTable('users_archive') && !Schema::hasColumn('users_archive', 'library_section')) {
            Schema::table('users_archive', function (Blueprint $table) {
                $table->string('library_section', 50)->default('entrance')->after('section');
                $table->index('library_section');
            });
        }

        // Add section column to attendance table if it doesn't exist
        if (!Schema::hasColumn('attendance', 'library_section')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->string('library_section', 50)->default('entrance')->after('user_id');
                $table->index('library_section');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop section columns
        if (Schema::hasColumn('users', 'library_section')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('library_section');
            });
        }

        if (Schema::hasTable('users_archive') && Schema::hasColumn('users_archive', 'library_section')) {
            Schema::table('users_archive', function (Blueprint $table) {
                $table->dropColumn('library_section');
            });
        }

        if (Schema::hasColumn('attendance', 'library_section')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dropColumn('library_section');
            });
        }

        // Drop sections table
        Schema::dropIfExists('sections');
    }
};
