<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->string('platform')->after('id');
            $table->dateTime('expiry_date')->nullable()->change();
        });

        Schema::table('user_promo_codes', function (Blueprint $table) {
            $table->string('unique_id')->after('id');
        });

        Schema::table('user_wallet_payments', function (Blueprint $table) {
            $table->string('promo_code')->after('status')->nullable();
            $table->string('promo_discount')->after('promo_code')->default(0);
        });

        Schema::table('audio_call_payments', function (Blueprint $table) {
            $table->string('promo_code')->after('status')->nullable();
            $table->string('promo_discount')->after('promo_code')->default(0);
        });

        Schema::table('video_call_payments', function (Blueprint $table) {
            $table->string('promo_code')->after('status')->nullable();
            $table->string('promo_discount')->after('promo_code')->default(0);
        });

        Schema::table('live_video_payments', function (Blueprint $table) {
            $table->string('promo_code')->after('status')->nullable();
            $table->string('promo_discount')->after('promo_code')->default(0);
        });

        Schema::table('chat_asset_payments', function (Blueprint $table) {
            $table->string('promo_code')->after('status')->nullable();
            $table->string('promo_discount')->after('promo_code')->default(0);
        });

        Schema::table('lss_product_payments', function (Blueprint $table) {
            $table->string('promo_code')->after('status')->nullable();
            $table->string('promo_discount')->after('promo_code')->default(0);
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('user_paid_amount')->after('message');
            $table->string('promo_code')->after('user_paid_amount')->nullable();
            $table->string('promo_discount')->after('promo_code')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('description')->after('title');
            $table->dropColumn('platform');
            $table->dateTime('expiry_date')->change();
        });

        Schema::table('user_promo_codes', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });

        Schema::table('user_wallet_payments', function (Blueprint $table) {
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_discount');
        });

        Schema::table('audio_call_payments', function (Blueprint $table) {
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_discount');
        });

        Schema::table('video_call_payments', function (Blueprint $table) {
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_discount');
        });

        Schema::table('live_video_payments', function (Blueprint $table) {
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_discount');
        });

        Schema::table('chat_asset_payments', function (Blueprint $table) {
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_discount');
        });

        Schema::table('lss_product_payments', function (Blueprint $table) {
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_discount');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('user_paid_amount');
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_discount');
        });
        
    }
}