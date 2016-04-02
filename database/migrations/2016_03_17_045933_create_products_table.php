<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('seller_id')->unsigned()->index();
            $table->string('upc', 40);  // usually 12 digits but we give extra just in case.
            $table->string('sku', 60);
            $table->string('style', 60);    // vendor sku
            $table->string('gtin', 60);
            $table->string('product_name', 60);
            $table->string('product_description', 255);
            $table->decimal('cost', 5, 2);
            $table->decimal('price', 5, 2);
            $table->timestamps();
        });

        Schema::table('products', function ($table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
    }
}
