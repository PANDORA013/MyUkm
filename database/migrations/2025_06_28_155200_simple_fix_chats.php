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

        // Update group_id menggunakan cara yang sederhana
        $groups = DB::table('groups')->pluck('id', 'referral_code');
        
        foreach ($groups as $code => $groupId) {
            DB::table('chats')
                ->where('group_code', $code)
                ->update(['group_id' => $groupId]);
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
        // Tambahkan kembali kolom group_code jika dibutuhkan
        if (!Schema::hasColumn('chats', 'group_code')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->string('group_code')->nullable()->after('user_id');
            });
        }

        // Update group_code berdasarkan group_id
        $groups = DB::table('groups')->pluck('referral_code', 'id');
        
        foreach ($groups as $id => $code) {
            DB::table('chats')
                ->where('group_id', $id)
                ->update(['group_code' => $code]);
        }

        // Hapus kolom group_id
        if (Schema::hasColumn('chats', 'group_id')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->dropColumn('group_id');
            });
        }
    }
};
