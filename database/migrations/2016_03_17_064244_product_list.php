<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('list_name');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('supplier_id')->unsigned()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('product_lists', function ($table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('supplier_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_lists');
    }
}
