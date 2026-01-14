<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            // Nothing to fix if table doesn't exist
            return;
        }

        // Ensure read_at exists (defensive)
        if (!Schema::hasColumn('notifications', 'read_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('data');
            });
        }

        // If there's a `title` column that is NOT NULL, make it nullable to avoid failing inserts
        if (Schema::hasColumn('notifications', 'title')) {
            try {
                // Use a raw ALTER to avoid requiring doctrine/dbal for simple modifications
                DB::statement("ALTER TABLE `notifications` MODIFY `title` VARCHAR(255) NULL");
            } catch (\Exception $e) {
                // Log but don't hard-fail migration here
                // (Logging isn't available in migrations in some contexts, so swallow)
            }
        }
    }

    public function down(): void
    {
        // Intentionally left empty: reversible only by manual DBA action if needed.
    }
};
