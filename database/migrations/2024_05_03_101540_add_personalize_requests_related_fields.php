<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonalizeRequestsRelatedFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('personalized_requests', function (Blueprint $table) {
            $table->string('preview_file')->after('file');
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
            $table->dropColumn('preview_file');
        });
    }
}
