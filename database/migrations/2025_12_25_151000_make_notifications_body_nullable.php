<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Make `id` accept UUIDs and `title`/`body` nullable if they exist. Use raw SQL for MySQL compatibility.
        $driver = DB::getDriverName();
        if (Schema::hasTable('notifications')) {
            // Ensure `id` column can store UUID strings (36 chars). This avoids "Data truncated for column 'id'" when
            // Laravel inserts a UUID for notifications.
            if (Schema::hasColumn('notifications', 'id')) {
                if ($driver === 'mysql') {
                    DB::statement('ALTER TABLE `notifications` MODIFY `id` VARCHAR(36) NOT NULL');
                } else {
                    Schema::table('notifications', function ($table) {
                        $table->string('id', 36)->change();
                    });
                }
            }

            if (Schema::hasColumn('notifications', 'body')) {
                if ($driver === 'mysql') {
                    DB::statement('ALTER TABLE `notifications` MODIFY `body` TEXT NULL');
                } else {
                    Schema::table('notifications', function ($table) {
                        $table->text('body')->nullable()->change();
                    });
                }
            }

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
    }

    public function down()
    {
        if (!Schema::hasTable('notifications')) return;

        $driver = DB::getDriverName();
        // Revert `id` back to a numeric auto-increment if needed, and make `body`/`title` not nullable again.
        if (Schema::hasColumn('notifications', 'id')) {
            if ($driver === 'mysql') {
                // Convert back to a big integer auto-increment primary key.
                DB::statement('ALTER TABLE `notifications` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            } else {
                Schema::table('notifications', function ($table) {
                    $table->bigIncrements('id')->change();
                });
            }
        }

        if (Schema::hasColumn('notifications', 'body')) {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `notifications` MODIFY `body` TEXT NOT NULL');
            } else {
                Schema::table('notifications', function ($table) {
                    $table->text('body')->nullable(false)->change();
                });
            }
        }

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
