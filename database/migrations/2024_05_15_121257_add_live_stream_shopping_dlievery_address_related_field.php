<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLiveStreamShoppingDlieveryAddressRelatedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('lss_delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('user_id');
            $table->string('name')->default('');
            $table->text('address')->nullable();
            $table->string('pincode')->default('');
            $table->string('city')->nullable();
            $table->string('state')->default('');
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('landmark')->default('');
            $table->string('contact_number')->nullable();
            $table->tinyInteger('is_default')->default(NO);
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

        Schema::table('lss_product_payments', function (Blueprint $table) {
           $table->foreignId('lss_delivery_address_id')->after('user_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('lss_delivery_addresses');

      Schema::table('lss_product_payments', function (Blueprint $table) {
            $table->dropColumn('lss_delivery_address_id');
        });
    }
}
