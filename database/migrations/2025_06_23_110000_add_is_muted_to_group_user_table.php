<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            if (!Schema::hasColumn('group_user', 'is_muted')) {
                $table->boolean('is_muted')->default(false)->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            if (Schema::hasColumn('group_user', 'is_muted')) {
                $table->dropColumn('is_muted');
            }
        });
    }
};
