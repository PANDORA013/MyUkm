<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            // Add composite index for faster querying of chats by group
            $table->index(['group_id', 'created_at']);
            // Add index for read status queries
            $table->index(['group_id', 'user_id', 'read_at']);
        });
    }

    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex(['group_id', 'created_at']);
            $table->dropIndex(['group_id', 'user_id', 'read_at']);
        });
    }
};
