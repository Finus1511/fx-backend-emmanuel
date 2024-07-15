<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLiveStreamShoppingRelatedMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_stream_shoppings', function (Blueprint $table) {
            $table->integer('viewer_count')->after('end_time')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('live_stream_shoppings', function (Blueprint $table) {
            $table->dropColumn('viewer_count');
        });
    }
}
