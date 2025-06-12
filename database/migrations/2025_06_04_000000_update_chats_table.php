<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambah foreign key constraint untuk group_code
        Schema::table('chats', function (Blueprint $table) {
            // Backup data yang ada
            $table->foreignId('group_id')->nullable()->after('user_id');
            
            // Buat indeks untuk optimasi query
            $table->index(['group_code', 'created_at']);
            $table->index(['user_id', 'created_at']);
            
            // Tambah soft deletes
            $table->softDeletes();
        });

        // Update group_id berdasarkan group_code
        DB::statement('
            UPDATE chats c
            INNER JOIN groups g ON c.group_code = g.referral_code
            SET c.group_id = g.id
        ');

        // Buat foreign key setelah data diupdate
        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->dropColumn('group_code');
        });
    }

    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropIndex(['group_code', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropSoftDeletes();
            $table->string('group_code')->after('user_id');
            $table->dropColumn('group_id');
        });
    }
};
