<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('user_id');
            $table->string('name')->default('');
            $table->longText('description');
            $table->string('thumbnail')->default(asset('placeholder.jpeg'));
            $table->float('amount')->default(0.00);
            $table->float('token')->default(0.00);
            $table->tinyInteger('is_paid')->default(UNPAID);
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });
        Schema::create('collection_files', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('user_id');
            $table->string('collection_id')->default('');
            $table->string('file');
            $table->string('file_type')->default(FILE_TYPE_IMAGE);
            $table->string('preview_file')->default("");            
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

        Schema::create('collection_payments', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->foreignId('user_id');
            $table->foreignId('collection_id');
            $table->string('payment_id');
            $table->string('payment_mode')->default(CARD);
            $table->float('amount')->default(0.00);
            $table->float('admin_amount')->default(0.00);
            $table->float('user_amount')->default(0.00);
            $table->string('currency')->default('$');
            $table->tinyInteger('status')->default(PAID);
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
        Schema::dropIfExists('collections');
        Schema::dropIfExists('collection_files');
        Schema::dropIfExists('collection_payments');
    }
}
