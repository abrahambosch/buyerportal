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
        $fields = [
            "factory" => "Factory",
            'style' => 'Item#',
            'product_description' => 'Description',
            'dimentions_json' => 'Dimentions',
            "master_pack" => "Master Pack",
            "cube" => "Cube (ft2)",
            "packing" => "Packing",
            "quantity" => "Qty",
            "unit_cost" => "POE",    // unit cost
            "fob" => "FOB",
            "total" => "Total $",
            "total_cft" => "Total CFT",
            "total_cmb" => "Total CMB",
            "unit_retail" => "Unit Retail",
            "notes" => "Production Notes",
            "fob_cost" => "FOB (Cost)",
            "frt" => "FRT",
            "duty" => "Duty",
            "elc" => "ELC",
            "poe_percent" => "POE%",
            "fob_percent" => "FOB%",
            "hts" => "HTS",
            "duty_percent" => "Duty %",
            "port" => "Port",
            "weight" => "Weight (kg)",
            'upc'=>'Cust UPC',
            'sku' => 'Cust SKU',
            'material' => 'Material',
            'factory_item' => 'Factory Item #',
            'samples_requested' => 'Samples Requested',
            'carton_size_l' => 'Carton Size L(")',
            'carton_size_w' => 'Carton Size W(")',
            'carton_size_h' => 'Carton Size H(")',
            'factory_lead_time' => 'Factory Lead Time',
        ];



        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('seller_id')->unsigned()->index();
            
            $table->string('factory', 60)->nullable();
            $table->string('style', 60);    // vendor sku
            $table->string('product_description', 255)->nullable();
            $table->string('dimentions_json', 255)->nullable();
            $table->integer('master_pack')->default(0);
            $table->decimal('cube', 12, 2)->default(0);
            $table->string('packing', 60)->nullable();
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

            $table->string('gtin', 60)->nullable();
            $table->string('product_name', 60)->nullable();

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
