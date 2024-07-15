<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLiveVideosRelatedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('live_videos', function (Blueprint $table) {
            $table->string('host_id')->nullable()->after('port_no');
        });  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_videos', function (Blueprint $table) {
            $table->dropColumn('host_id');
        });
    }
}
