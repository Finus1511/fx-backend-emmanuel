<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_tips', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('title')->default('');
            $table->longText('description');
            $table->string('picture')->default(asset('placeholder.jpeg'));
            $table->float('amount')->default(0.00);
            $table->string('type')->default(LIVE_VIDEO_PAYMENTS);
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
        Schema::dropIfExists('custom_tips');
    }
}
