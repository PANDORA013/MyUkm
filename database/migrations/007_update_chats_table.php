<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        // Update group_id berdasarkan group_code (SQLite compatible)
        $chats = DB::table('chats')->get();
        foreach ($chats as $chat) {
            $group = DB::table('groups')->where('referral_code', $chat->group_code)->first();
            if ($group) {
                DB::table('chats')->where('id', $chat->id)->update(['group_id' => $group->id]);
            }
        }

        // Buat foreign key setelah data diupdate
        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->dropColumn('group_code');
        });
    }

    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            // Hapus foreign key constraint
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['group_id']);
            }
            
            // Hapus indeks jika ada
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('chats');
            
            if (array_key_exists('chats_group_code_created_at_index', $indexesFound)) {
                $table->dropIndex(['group_code', 'created_at']);
            }
            
            if (array_key_exists('chats_user_id_created_at_index', $indexesFound)) {
                $table->dropIndex(['user_id', 'created_at']);
            }
            
            // Hapus soft deletes jika ada
            if (Schema::hasColumn('chats', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            
            // Tambahkan kembali kolom group_code
            if (!Schema::hasColumn('chats', 'group_code')) {
                $table->string('group_code')->after('user_id')->nullable();
            }
            
            // Hapus kolom group_id jika ada
            if (Schema::hasColumn('chats', 'group_id')) {
                $table->dropColumn('group_id');
            }
        });
    }
};
