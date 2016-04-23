<?php

namespace App\Services\Import;

use App\PurchaseOrder;
use App\Product;
use App\Services\ProductService;

class BerlingtonImportService implements ImportServiceInterface
{
    protected $controller = null;
    protected $create_offer = false;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function setController($c)
    {
        $this->controller = $c;
    }

    function importSave($filename, $user_id, $seller_id, $create_offer=false)
    {
        $this->create_offer = $create_offer;
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

            $this->purchaseOrder = null;
            if ($this->create_offer) {
                if (preg_match('/#\s*(\d+)\s*$/', $po_num_line[1], $matches)) {
                    $po_num = $matches[1];
                    $time = false;
                    if (preg_match('|(\d+/\d+/\d+)|', $po_date_line[1], $matches)) {
                        $time = strtotime($matches[1]);
                    }
                    if ($time === false) {
                        $time = time();
                    }
                    $order_date = date("Y-m-d", $time);

                    $poArr = [
                        'po_num' => $po_num,
                        'order_date' => $order_date,
                        'buyer_id' => $user_id,
                        'seller_id' => $seller_id
                    ];
                        echo "here1<br>";
                    $this->purchaseOrder = $this->productService->updateOrCreatePurchaseOrder($poArr);
                    echo "here2<br>";
                    if (empty($this->purchaseOrder)) {
                        dd("failed to create purchase order. ");
                    }
                }
                else {
                    dd("didn't find the po num");
                }
            }

            while (($row = fgetcsv($handle, 0, $delimiter, '"')) !== FALSE) {
                if (!empty($row[$field_map['style']]) && !empty($row[$field_map['quantity']])) {
                    $product = $this->makeProductFromRow($row, $user_id, $seller_id);
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

    protected function makeProductFromRow($row, $user_id, $seller_id)
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
            $product = $this->productService->createProductIfNotExists($productArr);
            if ($this->create_offer && !empty($this->purchaseOrder) && !empty($product)) {
                $productArr['purchase_order_id'] = $this->purchaseOrder->id;
                $productArr['product_id'] = $product->product_id;
                unset($productArr['user_id']);
                unset($productArr['seller_id']);
                $purchaseOrderItem = $this->productService->updateOrCreatePurchaseOrderItem($productArr);
                if (!$purchaseOrderItem) dd("failed to create item from product", $productArr);
            }
            return $product;
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
}