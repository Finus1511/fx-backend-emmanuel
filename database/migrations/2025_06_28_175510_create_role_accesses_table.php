<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default("");
            $table->foreignId('admin_id');
            $table->text('roles');
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->enum('role',[ADMIN, SUB_ADMIN])->after('gender')->default(ADMIN);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_accesses');

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
