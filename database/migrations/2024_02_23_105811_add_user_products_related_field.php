<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserProductsRelatedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('user_products', function (Blueprint $table) {
            $table->tinyInteger('is_digital_product')->default(NO)->after('is_visible');
            $table->string('product_file')->default(asset('placeholder.jpeg'))->after('picture');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_digital_product');
            $table->dropColumn('product_file');
        });
    }
}
