<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLssDeliveryAddressesRelatedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('lss_delivery_addresses', function (Blueprint $table) {
           $table->string('region_code')->after('state')->nullable();
        });

        Schema::table('lss_product_payments', function (Blueprint $table) {
           $table->tinyInteger('is_shipped')->after('shipping_url')->default(NO);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lss_delivery_addresses', function (Blueprint $table) {
            $table->dropColumn('region_code');
        });

        Schema::table('lss_product_payments', function (Blueprint $table) {
           $table->dropColumn('is_shipped');
        });
    }
}
