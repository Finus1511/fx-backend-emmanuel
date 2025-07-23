<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // attributes table
        // Schema::create('attributes', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('unique_id')->default("");
        //     $table->string('name'); // e.g., Color, Size
        //     $table->timestamps();
        // });

        // // attribute_values table
        // Schema::create('attribute_values', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('unique_id')->default("");
        //     $table->foreignId('attribute_id')->constrained();
        //     $table->string('value'); // e.g., Red, Large
        //     $table->timestamps();
        // });

        // product_variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default("");
            $table->foreignId('user_product_id')->constrained();
            $table->json('attributes'); // { "color": "red", "size": "M" }
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->after('user_product_id')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variants');

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('product_variant_id');
        });
    }
}
