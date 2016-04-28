<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->date('order_date');
            $table->string('po_num', 60);
            $table->string('ethercalc_id', 60);
            $table->string('po_type', 10)->default("po");   // po or quote
            $table->string('buyer_notes')->nullable();
            $table->string('supplier_notes')->nullable();
            $table->integer('buyer_id')->unsigned()->index();
            $table->integer('supplier_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::table('purchase_orders', function ($table) {
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
        Schema::drop('purchase_orders');
    }
}
