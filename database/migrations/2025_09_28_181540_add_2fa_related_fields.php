<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add2faRelatedFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table){
            $table->string('mobile_otp')->nullable()->after('email_verified_at');
            $table->BigInteger('mobile_otp_expiry')->nullable()->after('mobile_otp');
            $table->string('google2fa_secret')->nullable()->after('mobile_otp_expiry');
            $table->tinyInteger('is_2fa_enabled')->default(NO)->after('google2fa_secret');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('mobile_otp');
            $table->dropColumn('mobile_otp_expiry');
            $table->dropColumn('google2fa_secret');
            $table->dropColumn('is_2fa_enabled');
        });
    }
}
