<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('labs', function (Blueprint $table) {
            if (!Schema::hasColumn('labs', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('labs', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down()
    {
        Schema::table('labs', function (Blueprint $table) {
            if (Schema::hasColumn('labs', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('labs', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
