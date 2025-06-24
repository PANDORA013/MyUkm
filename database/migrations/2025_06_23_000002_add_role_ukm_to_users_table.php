<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('anggota');
            $table->unsignedBigInteger('ukm_id')->nullable()->after('role');
            $table->foreign('ukm_id')->references('id')->on('ukms')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ukm_id']);
            $table->dropColumn(['role', 'ukm_id']);
        });
    }
};
