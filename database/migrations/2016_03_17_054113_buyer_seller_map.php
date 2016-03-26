<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuyerSellerMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_seller_map', function (Blueprint $table) {
            $table->increments('buyer_seller_map_id');
            $table->integer('buyer_id')->unsigned()->index();;
            $table->integer('seller_id')->unsigned()->index();;
            $table->timestamps();
        });

        Schema::table('buyer_seller_map', function ($table) {
            $table->foreign('buyer_id')->references('id')->on('users');
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
        Schema::drop('buyer_seller_map');
    }
}
