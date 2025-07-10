<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukm_deletions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ukm_id')->nullable();
            $table->string('ukm_name');
            $table->string('ukm_code', 10)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->string('deletion_reason')->nullable();
            $table->timestamps();

            $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukm_deletions');
    }
};
