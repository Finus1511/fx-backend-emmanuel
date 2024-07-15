<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileTypeFieldsToChatMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('file_type')->default(FILE_TYPE_TEXT);
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
            $table->dropColumn('file_type');
        });
    }
}
