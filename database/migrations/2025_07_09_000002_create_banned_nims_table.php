<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('banned_nims', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique(); // NIM yang dibanned
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete(); // User yang melakukan ban (idiomatik Laravel)
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('banned_nims');
    }
};
