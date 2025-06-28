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

        // Update group_id based on group_code (Fully SQLite compatible)
        // First, get all chats with their corresponding group IDs
        $groupMappings = DB::table('groups')
            ->select('id', 'referral_code')
            ->whereNotNull('referral_code')
            ->pluck('id', 'referral_code')
            ->toArray();
        
        // Then update each chat in batches to be more efficient
        DB::table('chats')
            ->whereNotNull('group_code')
            ->orderBy('id')
            ->chunk(100, function ($chats) use ($groupMappings) {
                $updates = [];
                
                foreach ($chats as $chat) {
                    if (isset($groupMappings[$chat->group_code])) {
                        $updates[] = [
                            'id' => $chat->id,
                            'group_id' => $groupMappings[$chat->group_code]
                        ];
                    }
                }
                
                // Update in batches
                foreach (array_chunk($updates, 100) as $batch) {
                    foreach ($batch as $update) {
                        DB::table('chats')
                            ->where('id', $update['id'])
                            ->update(['group_id' => $update['group_id']]);
                    }
                }
            });

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
