<?php

namespace App\Services;

use App\Product;

class ImportService
{
    protected $controller = null;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function setController($c)
    {
        $this->controller = $c;
    }
    
    function csvImportSave($filename, $user_id, $seller_id)
    {
        $products = $this->csv_to_array($filename);
        foreach ($products as $product) {
            $product['user_id'] = $user_id;
            $product['seller_id'] = $seller_id;
            print_r($product); echo "<br>";
            $this->createProductIfNotExists($product);
        }
    }

    function createProductIfNotExists($productArr)
    {
        $product = Product::where(['user_id' => $productArr['user_id'],'seller_id' => $productArr['seller_id'], 'style' => $productArr['style']])->first();
        if (!$product) {
            $product = Product::create($productArr);
            return $product;
        }
        return $product;
    }

    function berlingtonImportSave($filename, $user_id, $seller_id)
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $field_map = $this->getBerlingtonCsvFieldMap();
        $header = NULL;
        $data = array();
        $delimiter = ",";
        if (($handle = fopen($filename, 'r')) !== FALSE) {

            if (($po_num_line = fgetcsv($handle, 0, $delimiter, '"')) === FALSE) return;
            if (($po_date_line = fgetcsv($handle, 0, $delimiter, '"')) === FALSE) return;
            if (($row = fgetcsv($handle, 0, $delimiter, '"')) === FALSE) return;
            if (($row = fgetcsv($handle, 0, $delimiter, '"')) === FALSE) return;
            if (($row = fgetcsv($handle, 0, $delimiter, '"')) === FALSE) return;
            if (($header = fgetcsv($handle, 0, $delimiter, '"')) === FALSE) return;
            $last_product = null;
            while (($row = fgetcsv($handle, 0, $delimiter, '"')) !== FALSE) {
                if (!empty($row[$field_map['style']]) && !empty($row[$field_map['quantity']])) {
                    $product = $this->makeProductFromBerlingtonRow($row, $user_id, $seller_id);
                    if ($product) $last_product = $product;
                }
                else if (!empty($last_product) && !empty($row[3]) && !empty($row[4]) && !empty($row[5]) && !empty($row[6])) {   // dymentions for subitems
                    $dimentions_json = $last_product->dimentions_json;
                    $dimentions_json_arr = !empty($dimentions_json)?json_decode($dimentions_json, true):[];
                    if (!is_array($dimentions_json_arr)) $dimentions_json_arr = [];
                    $dimentions_json_arr[] = ['description' => $row[3], 'length' => $row[4], 'width' => $row[5], 'height' => $row[6]];
                    $last_product->dimentions_json = json_encode($dimentions_json_arr);
                    $last_product->save();
                }
            }
        }
    }

    protected function makeProductFromBerlingtonRow($row, $user_id, $seller_id)
    {
        $field_map = $this->getBerlingtonCsvFieldMap();
        $productArr = [];
        foreach ($field_map as $field=>$i) {
            if (!empty($row[$i])) {
                $productArr[$field] = trim($row[$i]);
            }
        }
        $productArr['user_id'] = $user_id;
        $productArr['seller_id'] = $seller_id;
        $productArr = $this->productService->sanatizeProductArr($productArr);
        //print "read product: " . print_r($productArr, true ) . "<br>";
        if ($this->isValidProductArray($productArr)) {
            //print "creating product: <br>";
            return $this->createProductIfNotExists($productArr);
        }
        else {
            //echo "invalid product<br>";
        }

        return null;
    }

    


    protected function isValidProductArray($arr)
    {
        if (empty($arr['style'])) {
            return false;
        }
        return true;
    }

    protected function getBerlingtonCsvFieldMap()
    {
        $field_map = [
            "factory" => 0,
            'style' => 2,
            'product_description' => 3,
            'dimentions_json' => 4,
            "master_pack" => 7,
            "cube" => 8,
            "packing" => 9,
            "quantity" => 10,
            "unit_cost" => 11,    // unit cost
            "fob" => 12,
            "total" => 13,
            "total_cft" => 14,
            "total_cmb" => 15,
            "unit_retail" => 17,
            "notes" => 18,
            "fob_cost" => 19,
            "frt" => 20,
            "duty" => 21,
            "elc" => 22,
            "poe_percent" => 23,
            "fob_percent" => 24,
            "hts" => 38,
            "duty_percent" => 39,
            "port" => 40,
            "weight" => 41,
            'upc'=> 42,
            'sku' => 43,
            'material' => 44,
            'factory_item' => 45,
            'samples_requested' => 46,
            'carton_size_l' => 47,
            'carton_size_w' => 48,
            'carton_size_h' => 49,
            'factory_lead_time' => 50,
        ];
        return $field_map;
    }


    protected function csv_to_array($filename='', $delimiter=',')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter, '"')) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else if (count($row) == count($header)){
                    print_r($header) . "<br>";
                    print_r($row) . "<br>";
                    $data[] = array_combine($header, $row);
                }

            }
            fclose($handle);
        }
        return $data;
    }


}