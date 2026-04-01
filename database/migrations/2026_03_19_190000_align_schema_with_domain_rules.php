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
        if (Schema::hasTable('cvs') && Schema::hasColumn('cvs', 'user_id')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });
        }

        $coverTable = Schema::hasTable('cover') ? 'cover' : (Schema::hasTable('covers') ? 'covers' : null);
        if ($coverTable !== null && Schema::hasColumn($coverTable, 'content')) {
            Schema::table($coverTable, function (Blueprint $table) {
                $table->text('content')->change();
            });
        }

        $eventTable = Schema::hasTable('event') ? 'event' : (Schema::hasTable('events') ? 'events' : null);
        if ($eventTable !== null && Schema::hasColumn($eventTable, 'event_date')) {
            Schema::table($eventTable, function (Blueprint $table) {
                $table->date('event_date')->change();
            });
        }

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
        if (Schema::hasTable('cvs') && Schema::hasColumn('cvs', 'user_id')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }

        $coverTable = Schema::hasTable('cover') ? 'cover' : (Schema::hasTable('covers') ? 'covers' : null);
        if ($coverTable !== null && Schema::hasColumn($coverTable, 'content')) {
            Schema::table($coverTable, function (Blueprint $table) {
                $table->string('content')->change();
            });
        }

        $eventTable = Schema::hasTable('event') ? 'event' : (Schema::hasTable('events') ? 'events' : null);
        if ($eventTable !== null && Schema::hasColumn($eventTable, 'event_date')) {
            Schema::table($eventTable, function (Blueprint $table) {
                $table->dateTime('event_date')->change();
            });
        }

        if (! Schema::hasColumn('applications', 'stage_order')) {
            Schema::table('applications', function ($table) {
                $table->string('stage_order')->default('0');
            });
        }
    }
};
