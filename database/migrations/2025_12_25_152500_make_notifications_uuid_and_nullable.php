<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $driver = DB::getDriverName();

        if (! Schema::hasTable('notifications')) {
            return;
        }

        // Make `id` accept UUID strings (36 chars) to avoid data truncation when inserting UUIDs.
        if (Schema::hasColumn('notifications', 'id')) {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `notifications` MODIFY `id` VARCHAR(36) NOT NULL');
            } else {
                Schema::table('notifications', function ($table) {
                    $table->string('id', 36)->change();
                });
            }
        }

        // Make `body` nullable
        if (Schema::hasColumn('notifications', 'body')) {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `notifications` MODIFY `body` TEXT NULL');
            } else {
                Schema::table('notifications', function ($table) {
                    $table->text('body')->nullable()->change();
                });
            }
        }

        // Make `title` nullable
        if (Schema::hasColumn('notifications', 'title')) {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `notifications` MODIFY `title` VARCHAR(191) NULL');
            } else {
                Schema::table('notifications', function ($table) {
                    $table->string('title')->nullable()->change();
                });
            }
        }
    }

    public function down()
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        $driver = DB::getDriverName();

        // Revert `id` to BIGINT UNSIGNED AUTO_INCREMENT if possible
        if (Schema::hasColumn('notifications', 'id')) {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `notifications` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            } else {
                Schema::table('notifications', function ($table) {
                    $table->bigIncrements('id')->change();
                });
            }
        }

        // Make `body` NOT NULL
        if (Schema::hasColumn('notifications', 'body')) {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `notifications` MODIFY `body` TEXT NOT NULL');
            } else {
                Schema::table('notifications', function ($table) {
                    $table->text('body')->nullable(false)->change();
                });
            }
        }

        // Make `title` NOT NULL
        if (Schema::hasColumn('notifications', 'title')) {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `notifications` MODIFY `title` VARCHAR(191) NOT NULL');
            } else {
                Schema::table('notifications', function ($table) {
                    $table->string('title')->nullable(false)->change();
                });
            }
        }
    }
};
