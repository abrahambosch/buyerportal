<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuyerSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_supplier', function (Blueprint $table) {
            $table->increments('buyer_supplier_id');
            $table->integer('buyer_id')->unsigned()->index();;
            $table->integer('supplier_id')->unsigned()->index();;
            $table->timestamps();
        });

        Schema::table('buyer_supplier', function ($table) {
            $table->foreign('buyer_id')->references('id')->on('users');
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
        Schema::drop('buyer_supplier');
    }
}
