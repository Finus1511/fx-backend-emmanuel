<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChatMessagePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->float('chat_message_amount')->after('audio_call_token')->default(0.00);
            $table->float('chat_message_token')->default(0.00)->after('chat_message_amount');
        });

        Schema::create('chat_message_payments', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('user_id');
             $table->foreignId('to_user_id');
            $table->string('payment_id');
            $table->string('payment_mode')->default(CARD);
            $table->float('amount')->default(0.00);
            $table->float('admin_amount')->default(0.00);
            $table->float('user_amount')->default(0.00);
            $table->dateTime('paid_date')->nullable();
            $table->dateTime('expiry_date')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('chat_message_amount');
            $table->dropColumn('chat_message_token');
        });
        
        Schema::dropIfExists('chat_message_payments');
    }
}
