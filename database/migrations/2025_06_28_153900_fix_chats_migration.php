<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixChatsMigration extends Migration
{
    public function up()
    {
        // This migration will be used to fix the previous one
        // First, ensure the group_id column exists
        if (!Schema::hasColumn('chats', 'group_id')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->foreignId('group_id')->nullable()->after('user_id');
            });
        }

        // Get all chats with their group codes
        $chats = DB::table('chats')->whereNotNull('group_code')->get();
        
        foreach ($chats as $chat) {
            // Find the group for this chat
            $group = DB::table('groups')
                ->where('referral_code', $chat->group_code)
                ->first();
                
            if ($group) {
                // Update the chat with the group_id
                DB::table('chats')
                    ->where('id', $chat->id)
                    ->update(['group_id' => $group->id]);
            }
        }

        // Now that we've migrated the data, we can drop the group_code column
        // and add the foreign key constraint if they haven't been done yet
        Schema::table('chats', function (Blueprint $table) {
            if (Schema::hasColumn('chats', 'group_code')) {
                $table->dropColumn('group_code');
            }
            
            // Only add the foreign key if it doesn't exist
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = collect($sm->listTableForeignKeys('chats'));
            
            if (!$foreignKeys->contains('getName', 'chats_group_id_foreign')) {
                $table->foreign('group_id')
                    ->references('id')
                    ->on('groups')
                    ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        // In case we need to rollback, we'll need to add the group_code column back
        Schema::table('chats', function (Blueprint $table) {
            if (!Schema::hasColumn('chats', 'group_code')) {
                $table->string('group_code')->after('user_id')->nullable();
            }
            
            // Drop the foreign key if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = collect($sm->listTableForeignKeys('chats'));
            
            if ($foreignKeys->contains('getName', 'chats_group_id_foreign')) {
                $table->dropForeign('chats_group_id_foreign');
            }
        });
    }
}
