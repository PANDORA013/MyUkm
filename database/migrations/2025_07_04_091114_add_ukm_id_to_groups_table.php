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
        Schema::table('groups', function (Blueprint $table) {
            // Add ukm_id foreign key column
            $table->foreignId('ukm_id')->nullable()->constrained('ukms')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['ukm_id']);
            $table->dropColumn('ukm_id');
        });
    }
};
