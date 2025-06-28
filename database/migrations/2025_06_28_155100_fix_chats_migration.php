<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixChatsMigration extends Migration
{
    public function up()
    {
        // Check if the chats table has group_code column
        if (!Schema::hasColumn('chats', 'group_code')) {
            return;
        }

        // Update group_id using query builder instead of raw SQL
        $chats = DB::table('chats')
            ->select('chats.id', 'groups.id as group_id')
            ->leftJoin('groups', 'chats.group_code', '=', 'groups.referral_code')
            ->whereNotNull('chats.group_code')
            ->get();

        foreach ($chats as $chat) {
            if ($chat->group_id) {
                DB::table('chats')
                    ->where('id', $chat->id)
                    ->update(['group_id' => $chat->group_id]);
            }
        }

        // Add foreign key and drop column
        Schema::table('chats', function (Blueprint $table) {
            if (Schema::hasColumn('chats', 'group_code')) {
                $table->dropColumn('group_code');
            }
            
            // Add foreign key if it doesn't exist
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
        // Reverse the changes if needed
        Schema::table('chats', function (Blueprint $table) {
            // Drop foreign key if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = collect($sm->listTableForeignKeys('chats'));
            
            if ($foreignKeys->contains('getName', 'chats_group_id_foreign')) {
                $table->dropForeign('chats_group_id_foreign');
            }
            
            // Add back group_code column if it doesn't exist
            if (!Schema::hasColumn('chats', 'group_code')) {
                $table->string('group_code')->after('user_id')->nullable();
            }
        });
    }
}
