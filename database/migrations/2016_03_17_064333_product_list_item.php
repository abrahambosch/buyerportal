<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductListItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_list_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_list_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('product_list_items', function ($table) {
            $table->foreign('product_list_id')->references('id')->on('product_lists');
            $table->foreign('product_id')->references('product_id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_list_items');
    }
}
