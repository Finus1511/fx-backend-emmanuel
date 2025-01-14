<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default("");
            $table->foreignId('user_id');
            $table->string('name');
            $table->string('picture')->default(asset('community-placeholder.jpeg'));
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

        Schema::create('community_users', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default("");
            $table->foreignId('community_id');
            $table->foreignId('user_id');
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

        Schema::create('community_messages', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->string('reference_id')->nullable();
            $table->foreignId('from_user_id');
            $table->foreignId('community_id');
            $table->text('message');
            $table->string('is_file_uploaded')->default(NO);
            $table->string('file_type')->default(FILE_TYPE_TEXT);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        Schema::create('community_assets', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->foreignId('from_user_id');
            $table->foreignId('community_id');
            $table->foreignId('community_message_id');
            $table->string('file');
            $table->string('file_type')->default(FILE_TYPE_IMAGE);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('communities');
        Schema::dropIfExists('community_users');
        Schema::dropIfExists('community_messages');
        Schema::dropIfExists('community_assets');
    }
}
