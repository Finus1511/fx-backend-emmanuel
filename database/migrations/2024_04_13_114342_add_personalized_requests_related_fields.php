<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonalizedRequestsRelatedFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('personalized_requests', function (Blueprint $table) {
            $table->after('type', function($table) {
                $table->string('file');
                $table->string('file_type')->default(FILE_TYPE_IMAGE);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('personalized_requests', function (Blueprint $table) {
            $table->dropColumn('file');
            $table->dropColumn('file_type');
        });
    }
}
