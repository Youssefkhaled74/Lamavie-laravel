<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the `notifications` table if it does not exist, and ensure `read_at` exists.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                // morphs creates notifiable_type (string) and notifiable_id (unsignedBigInteger)
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('notifications', 'read_at')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->timestamp('read_at')->nullable()->after('data');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     * Preferably only drop the `read_at` column that we added; do not drop the whole table.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('notifications') && Schema::hasColumn('notifications', 'read_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                // dropping a column may fail on some DB drivers if dependent indexes exist;
                // this is the safest attempt to remove the column added by this migration.
                $table->dropColumn('read_at');
            });
        }
    }
};
