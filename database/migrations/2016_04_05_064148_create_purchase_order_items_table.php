<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_order_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();

            // duplicate product values. 
            $table->string('factory', 60)->nullable();
            $table->string('style', 60);    // vendor sku
            $table->string('product_description', 255)->nullable();
            $table->string('dimentions_json', 255)->nullable();
            $table->integer('master_pack')->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0);  // poe
            $table->decimal('fob', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('total_cft', 12, 2)->default(0);
            $table->decimal('total_cmb', 12, 2)->default(0);
            $table->decimal('unit_retail', 12, 2)->default(0);
            $table->decimal('notes')->nullable();
            $table->decimal('fob_cost', 12, 2)->default(0);
            $table->decimal('frt', 12, 2)->default(0);
            $table->decimal('duty', 12, 2)->default(0);
            $table->decimal('elc', 12, 2)->default(0);
            $table->decimal('poe_percent', 12, 2)->default(0);
            $table->decimal('fob_percent', 12, 2)->default(0);
            $table->string('hts', 50)->nullable();
            $table->decimal('duty_percent', 12, 2)->default(0);
            $table->string('port', 50)->nullable();
            $table->decimal('weight', 12, 2)->default(0);
            $table->string('upc', 40)->nullable();  // usually 12 digits but we give extra just in case.
            $table->string('sku', 60)->nullable();
            $table->string('material', 50)->nullable();
            $table->string('factory_item', 50)->nullable();
            $table->string('samples_requested', 255)->nullable();
            $table->decimal('carton_size_l', 12, 2)->default(0);
            $table->decimal('carton_size_w', 12, 2)->default(0);
            $table->decimal('carton_size_h', 12, 2)->default(0);
            $table->string('factory_lead_time', 255)->nullable();

            $table->timestamps();
        });

        Schema::table('purchase_order_items', function ($table) {
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
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
        Schema::drop('purchase_order_items');
    }
}
