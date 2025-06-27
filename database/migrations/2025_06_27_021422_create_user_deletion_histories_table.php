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
        Schema::create('user_deletion_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID pengguna yang dihapus');
            $table->string('name')->comment('Nama pengguna yang dihapus');
            $table->string('nim')->nullable()->comment('NIM pengguna yang dihapus');
            $table->string('email')->nullable()->comment('Email pengguna yang dihapus');
            $table->string('role')->nullable()->comment('Role pengguna saat dihapus');
            $table->text('deletion_reason')->nullable()->comment('Alasan penghapusan');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('ID admin yang menghapus');
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('deleted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_deletion_histories');
    }
};
