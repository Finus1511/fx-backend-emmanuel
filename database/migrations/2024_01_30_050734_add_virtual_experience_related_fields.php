<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVirtualExperienceRelatedFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('virtual_experiences', function (Blueprint $table) {
            $table->after('actual_end', function($table) {
                $table->string('agora_token')->default('');
                $table->string('virtual_id')->default('');
            });  
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
            $table->dropColumn('agora_token');
            $table->dropColumn('virtual_id');
        });
    }
}
