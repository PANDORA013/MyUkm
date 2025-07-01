<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_muted')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['group_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_user');
    }
};
