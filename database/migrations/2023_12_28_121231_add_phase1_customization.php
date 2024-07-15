<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhase1Customization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('virtual_experiences')) {        

            Schema::create('virtual_experiences', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default("");
                $table->foreignId('user_id');
                $table->string('title');
                $table->longText('description');
                $table->longText('notes')->nullable();
                $table->float('price_per')->default(0.00);
                $table->tinyInteger('total_capacity')->default(1);
                $table->tinyInteger('used_capacity')->default(1);
                $table->tinyInteger('onhold_capacity')->default(1);
                $table->tinyInteger('remaning_capacity')->default(1);
                $table->dateTime('scheduled_start')->nullable();
                $table->dateTime('scheduled_end')->nullable();
                $table->dateTime('actual_start')->nullable();
                $table->dateTime('actual_end')->nullable();
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            
            });
        }
        
        if(!Schema::hasTable('virtual_experience_bookings')) {        

            Schema::create('virtual_experience_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default("");
                $table->foreignId('virtual_experience_id');
                $table->foreignId('virtual_experience_user_id');
                $table->foreignId('user_id');
                $table->string('payment_id')->nullable();
                $table->float('price_per')->default(0.00);
                $table->tinyInteger('total_capacity')->default(1);
                $table->float('tax_amount')->default(0.00);
                $table->float('commission_amount')->default(0.00);
                $table->float('sub_total')->default(0.00);
                $table->float('total')->default(0.00);
                $table->dateTime('start')->nullable();
                $table->dateTime('end')->nullable();
                $table->string('payment_mode')->default(PAYMENT_OFFLINE);
                $table->string('currency')->default('$');
                $table->dateTime('paid_date')->nullable();
                $table->tinyInteger('is_failed')->default(0);
                $table->tinyInteger('failed_reason')->default(0);
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('virtual_experiences');
        
        Schema::dropIfExists('virtual_experience_bookings');
    }
}
