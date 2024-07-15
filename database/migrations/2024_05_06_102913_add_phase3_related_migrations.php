<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhase3RelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

            Schema::create('live_stream_shoppings', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->unique();
                $table->foreignId('user_id');
                $table->string('title')->default('');
                $table->string('stream_type')->default(STREAM_TYPE_PUBLIC);
                $table->string('payment_type')->default(PAYMENT_TYPE_FREE);
                $table->text('description')->nullabe();
                $table->float('amount')->default(0.00);
                $table->tinyInteger('is_streaming')->default(NO);
                $table->string('agora_token')->default('');
                $table->string('virtual_id')->default('');
                $table->tinyInteger('schedule_type')->default(SCHEDULE_TYPE_NOW);
                $table->datetime('schedule_time')->nullable();
                $table->string('preview_file')->default(asset('images/live-streaming.jpeg'));
                $table->string('preview_file_type')->default(FILE_TYPE_IMAGE);
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

            Schema::create('lss_products', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->unique();
                $table->foreignId('user_id');
                $table->foreignId('live_stream_shopping_id');
                $table->foreignId('user_product_id');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

            Schema::create('lss_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->unique();
                $table->foreignId('from_user_id');
                $table->foreignId('live_stream_shopping_id');
                $table->text('message')->nullable();
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

            Schema::create('lss_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->unique();
                $table->foreignId('user_id');
                $table->foreignId('live_stream_shopping_id');
                $table->string('payment_id');
                $table->string('payment_mode')->default(CARD);
                $table->float('amount')->default(0.00);
                $table->float('admin_amount')->default(0.00);
                $table->float('user_amount')->default(0.00);
                $table->string('currency')->default('$');
                $table->tinyInteger('status')->default(PAID);
                $table->timestamps();
            });

             Schema::create('lss_product_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->unique();
                $table->foreignId('user_id');
                $table->foreignId('live_stream_shopping_id');
                $table->foreignId('lss_product_id');
                $table->foreignId('user_product_id');
                $table->string('shipping_url')->default('');
                $table->string('payment_id');
                $table->string('payment_mode')->default(CARD);
                $table->float('amount')->default(0.00);
                $table->float('admin_amount')->default(0.00);
                $table->float('user_amount')->default(0.00);
                $table->string('currency')->default('$');
                $table->tinyInteger('status')->default(PAID);
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
      Schema::dropIfExists('live_stream_shopping');
      Schema::dropIfExists('lss_products');
      Schema::dropIfExists('lss_chat_messages');
      Schema::dropIfExists('lss_payments');
    }
}
