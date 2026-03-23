<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `cvs` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `cover` MODIFY `content` TEXT NOT NULL');
        DB::statement('ALTER TABLE `event` MODIFY `event_date` DATE NOT NULL');

        if (Schema::hasColumn('applications', 'stage_order')) {
            Schema::table('applications', function ($table) {
                $table->dropColumn('stage_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `cvs` MODIFY `user_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `cover` MODIFY `content` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `event` MODIFY `event_date` DATETIME NOT NULL');

        if (! Schema::hasColumn('applications', 'stage_order')) {
            Schema::table('applications', function ($table) {
                $table->string('stage_order')->default('0');
            });
        }
    }
};
