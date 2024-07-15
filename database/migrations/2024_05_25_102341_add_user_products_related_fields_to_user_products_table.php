<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserProductsRelatedFieldsToUserProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->tinyInteger('is_live_stream_product')->after('is_digital_product')->default(NO);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropColumn('is_live_stream_product');
        });
    }
}
