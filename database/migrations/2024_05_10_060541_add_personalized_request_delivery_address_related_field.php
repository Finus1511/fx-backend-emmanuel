<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonalizedRequestDeliveryAddressRelatedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personalized_delivery_addresses', function (Blueprint $table) {
            $table->string('country_code')->after('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('personalized_delivery_addresses', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }
}
