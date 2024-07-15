<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoupleOptionInGenderToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \DB::statement("ALTER TABLE `users` CHANGE `gender` `gender` ENUM('male','female', 'others','rather-not-select','couple') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'male';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `users` CHANGE `gender` `gender` ENUM('male','female', 'others','rather-not-select') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'male';");

    }
}
