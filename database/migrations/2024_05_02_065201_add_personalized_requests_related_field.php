<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonalizedRequestsRelatedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personalized_requests', function (Blueprint $table) {
            $table->foreignId('personalized_delivery_address_id')->after('receiver_id');
        });

        Schema::table('personalized_products', function (Blueprint $table) {
            $table->string('shipping_url')->after('personalized_request_id');
        });

        Schema::table('personalized_delivery_addresses', function (Blueprint $table) {
            $table->foreignId('user_id')->after('unique_id');
            $table->dropColumn('personalized_request_id');
        }); 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('personalized_requests', function (Blueprint $table) {
            $table->dropColumn('personalized_delivery_address_id');
        });

       Schema::table('personalized_products', function (Blueprint $table) {
            $table->dropColumn('shipping_url');
        });
    }
}
