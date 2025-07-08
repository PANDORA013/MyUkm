<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            // Add is_active column that tests are looking for
            if (!Schema::hasColumn('groups', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
