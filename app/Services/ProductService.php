<?php

namespace App\Services;

use App\Product;

class ProductService
{
    protected $controller = null;
    protected $fields = [];
    protected $buyer_fields = [];

    public function __construct()
    {
        $this->buyer_fields = [
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
        ];
        $this->fields = [
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
    }

    public function setController($c)
    {
        $this->controller = $c;
    }

    /**
     * list of all fields except user_id, seller_id, and id
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * fields a buyer should see
     * @return array
     */
    public function getBuyerFields()
    {
        return $this->buyer_fields;
    }

    /**
     * get fields that should be displayed on a listing page
     * @return array
     */
    public function getListingFields()
    {
        return $this->buyer_fields;
    }

    /**
     * get fields that should be displayed on a listing page
     * @return array
     */
    public function getBuyerListingFields()
    {
        return $this->buyer_fields;
    }

}