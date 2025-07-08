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
        Schema::create('user_deletions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deleted_user_id'); // ID user yang dihapus
            $table->string('deleted_user_name'); // Nama user yang dihapus
            $table->string('deleted_user_nim'); // NIM user yang dihapus
            $table->string('deleted_user_role'); // Role user yang dihapus
            $table->unsignedBigInteger('deleted_by'); // ID admin yang menghapus
            $table->string('deletion_reason')->nullable(); // Alasan penghapusan
            $table->text('deletion_notes')->nullable(); // Catatan tambahan
            $table->timestamps();
            
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['deleted_user_id', 'deleted_user_nim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_deletions');
    }
};
