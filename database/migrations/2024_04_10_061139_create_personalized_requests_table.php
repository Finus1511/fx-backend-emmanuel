<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalizedRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personalized_requests', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('sender_id');
            $table->foreignId('receiver_id');
            $table->enum('type', [PERSONALIZE_TYPE_IMAGE,PERSONALIZE_TYPE_VIDEO,PERSONALIZE_TYPE_AUDIO,PERSONALIZE_TYPE_PRODUCT])->default(PERSONALIZE_TYPE_IMAGE);
            $table->tinyInteger('product_type')->default(PRODUCT_TYPE_NONE);
            $table->float('amount')->default(0.00);
            $table->longText('description');
            $table->text('cancel_reason')->nullable();
            $table->tinyInteger('is_amount_update')->default(NO);
            $table->tinyInteger('status')->default(PERSONALIZE_USER_REQUESTED);
            $table->timestamps();
        });

        Schema::create('personalized_delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('personalized_request_id');
            $table->string('name')->default('');
            $table->text('address')->nullable();
            $table->string('pincode')->default('');
            $table->string('city')->nullable();
            $table->string('state')->default('');
            $table->string('country')->nullable();
            $table->string('landmark')->default('');
            $table->string('contact_number')->nullable();
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

         Schema::create('personalized_products', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('personalized_request_id');
            $table->foreignId('personalized_delivery_address_id');
            $table->string('name');
            $table->text('description');
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

        Schema::create('personalized_product_files', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('personalized_product_id');
            $table->string('file');
            $table->string('file_type')->default(FILE_TYPE_IMAGE);
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personalized_requests');
        Schema::dropIfExists('personalized_delivery_addresses');
        Schema::dropIfExists('personalized_products');
        Schema::dropIfExists('personalized_product_files');
    }
}
