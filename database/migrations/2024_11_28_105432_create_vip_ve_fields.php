<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVipVeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('ve_vips')) {        

            Schema::create('ve_vips', function (Blueprint $table) {
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
                $table->tinyInteger('status')->default(VIP_VE_SCHEDULED);
                $table->timestamps();
            
            });
        }

        if(!Schema::hasTable('ve_vip_questions')) {        

            Schema::create('ve_vip_questions', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default("");
                $table->foreignId('ve_vip_user_id');
                $table->foreignId('ve_vip_id');
                $table->longText('question');
                $table->string('type');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            
            });
        }

        if(!Schema::hasTable('ve_vip_answers')) {        

            Schema::create('ve_vip_answers', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default("");
                $table->foreignId('ve_vip_user_id');
                $table->foreignId('ve_vip_id');
                $table->foreignId('ve_vip_question_id');
                $table->foreignId('user_id');
                $table->longText('answer');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            
            });
        }
        
        if(!Schema::hasTable('ve_vip_bookings')) {        

            Schema::create('ve_vip_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default("");
                $table->foreignId('ve_vip_id');
                $table->foreignId('ve_vip_user_id');
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

        Schema::create('ve_vip_files', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->integer('user_id');
            $table->integer('ve_vip_id')->default(0);
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
        Schema::dropIfExists('ve_vips');
        Schema::dropIfExists('ve_vip_questions');
        Schema::dropIfExists('ve_vip_answers');
        Schema::dropIfExists('ve_vip_bookings');
        Schema::dropIfExists('ve_vip_files');
    }
}
