<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostsRelatedFieldsInPostFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_files', function (Blueprint $table) {
          $table->longText('youtube_link')->after('preview_file')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_files', function (Blueprint $table) {
           $table->dropColumn('youtube_link');
        });
    }
}