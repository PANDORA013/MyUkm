<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_passwords', function (Blueprint $table) {
            // Rename encrypted_password to password_enc that the code expects
            if (Schema::hasColumn('user_passwords', 'encrypted_password') && !Schema::hasColumn('user_passwords', 'password_enc')) {
                $table->renameColumn('encrypted_password', 'password_enc');
            }
        });
    }

    public function down()
    {
        Schema::table('user_passwords', function (Blueprint $table) {
            if (Schema::hasColumn('user_passwords', 'password_enc') && !Schema::hasColumn('user_passwords', 'encrypted_password')) {
                $table->renameColumn('password_enc', 'encrypted_password');
            }
        });
    }
};
