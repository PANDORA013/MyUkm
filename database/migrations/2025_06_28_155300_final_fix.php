<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Pastikan kolom group_id ada
        if (!Schema::hasColumn('chats', 'group_id')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->foreignId('group_id')->nullable()->after('user_id');
            });
        }

        // Update group_id dengan cara sederhana
        $chats = DB::table('chats')->whereNotNull('group_code')->get();
        
        foreach ($chats as $chat) {
            $group = DB::table('groups')
                ->where('referral_code', $chat->group_code)
                ->first();
                
            if ($group) {
                DB::table('chats')
                    ->where('id', $chat->id)
                    ->update(['group_id' => $group->id]);
            }
        }

        // Hapus kolom group_code jika sudah tidak diperlukan
        if (Schema::hasColumn('chats', 'group_code')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->dropColumn('group_code');
            });
        }
    }

    public function down()
    {
        // Tidak perlu implementasi down() untuk migrasi perbaikan
    }
};
