<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_archives', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default("");
            $table->integer('user_id');
            $table->string('origin')->default(FILE_ORIGIN_PROFILE); // post, live, audio/video call, chat asset, stories
            $table->string('file');
            $table->string('file_type')->default(FILE_TYPE_IMAGE);
            $table->float('amount')->default(0.00);
            $table->tinyInteger('is_paid')->default(NO);
            $table->integer('post_id')->default(0);
            $table->integer('story_id')->default(0);
            $table->integer('chat_asset_id')->default(0);
            $table->integer('live_video_id')->default(0);
            $table->integer('audio_call_request_id')->default(0);
            $table->integer('video_call_request_id')->default(0);
            $table->integer('user_product_id')->default(0);
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
        Schema::dropIfExists('file_archives');
    }
}
