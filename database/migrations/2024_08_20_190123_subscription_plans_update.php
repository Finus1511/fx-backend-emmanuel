<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SubscriptionPlansUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('user_id')->after('unique_id')->default(0);
            $table->longText('discount')->after('description')->nullable();
            $table->string('picture')->after('description')->default(asset('placeholder.jpeg'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('discount');
            $table->dropColumn('picture');
        });
    }
}
