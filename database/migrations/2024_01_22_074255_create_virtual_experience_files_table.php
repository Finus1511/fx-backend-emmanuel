<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualExperienceFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_experience_files', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('user_id');
            $table->integer('virtual_experience_id')->default(0);
            $table->string('file');
            $table->string('file_type')->default('image');
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
        Schema::dropIfExists('virtual_experience_files');
    }
}
