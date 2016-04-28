<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('filename')->nullable();
            $table->string('mime')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('order_num')->default(0);
            $table->integer('product_id')->unsigned()->nullable()->index();
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->integer('supplier_id')->unsigned()->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('media_items', function ($table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('supplier_id')->references('id')->on('users');
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
        Schema::drop('media_items');
    }
}
