<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualExperienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('ve_one_on_ones')) {        

            Schema::create('ve_one_on_ones', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default("");
                $table->foreignId('user_id');
                $table->string('title');
                $table->longText('description');
                $table->longText('notes')->nullable();
                $table->double('latitude',15,8)->default(0.000000);
                $table->double('longitude',15,8)->default(0.000000);
                $table->longText('location')->nullable();
                $table->float('amount')->default(0.00);
                $table->date('scheduled_date')->nullable();
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            
            });
        }
        
        if(!Schema::hasTable('ve_one_on_one_bookings')) {        

            Schema::create('ve_one_on_one_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default("");
                $table->foreignId('ve_one_on_one_id');
                $table->foreignId('ve_one_on_one_user_id');
                $table->foreignId('user_id');
                $table->string('payment_id')->nullable();
                $table->float('amount')->default(0.00);
                $table->float('tax_amount')->default(0.00);
                $table->float('commission_amount')->default(0.00);
                $table->float('sub_total')->default(0.00);
                $table->float('total')->default(0.00);
                $table->date('scheduled_date')->nullable();
                $table->string('payment_mode')->default(PAYMENT_OFFLINE);
                $table->string('currency')->default('$');
                $table->dateTime('paid_date')->nullable();
                $table->tinyInteger('is_failed')->default(0);
                $table->tinyInteger('failed_reason')->default(0);
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            
            });
        }

        Schema::create('ve_one_on_one_files', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('user_id');
            $table->integer('ve_one_on_one_id')->default(0);
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
        Schema::dropIfExists('ve_one_on_ones');
        Schema::dropIfExists('ve_one_on_one_bookings');
        Schema::dropIfExists('ve_one_on_one_files');
    }
}
