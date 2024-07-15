<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVirtualExperiencesRelatedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('virtual_experiences', function (Blueprint $table) {
            $table->string('host_id')->nullable()->after('virtual_id');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('virtual_experiences', function (Blueprint $table) {
            $table->dropColumn('host_id');
        });
    }
}
