<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChatRelatedfields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->integer('admin_id')->after('to_user_id');
        });

        Schema::table('chat_users', function (Blueprint $table) {
            $table->integer('admin_id')->after('to_user_id');
        });

        Schema::table('chat_assets', function (Blueprint $table) {
            $table->integer('admin_id')->after('to_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
        Schema::table('chat_users', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
        Schema::table('chat_assets', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
    }
}
