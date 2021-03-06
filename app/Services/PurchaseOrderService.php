<?php

namespace App\Services;

use App\Product;
use App\PurchaseOrder;

class PurchaseOrderService
{
    protected $controller = null;
    protected $fields = [];
    protected $po_fields = [];
    protected $buyer_fields = [];
    protected $field_types = [];

    public function __construct()
    {
        $this->po_fields = [
            'po_num' => 'Purchase Order #',
            'order_date' => 'Order Date',
        ];

        $this->buyer_fields = [
            'style' => 'Item#',
            'product_description' => 'Description',
            //'dimentions_json' => 'Dimentions',
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

        $this->field_types = [ // used by the importer to filter fields.
            "factory" => "string",
            'style' => "string",
            'product_description' => "string",
            'dimentions_json' => "string",
            "master_pack" => "integer",
            "cube" => "float",
            "packing" => "string",
            "quantity" => "integer",
            "unit_cost" => "float",    // unit cost
            "fob" => "float",
            "total" => "float",
            "total_cft" => "float",
            "total_cmb" => "float",
            "unit_retail" => "float",
            "notes" => "string",
            "fob_cost" => "float",
            "frt" => "float",
            "duty" => "float",
            "elc" => "float",
            "poe_percent" => "float",
            "fob_percent" => "float",
            "hts" => "string",
            "duty_percent" => "float",
            "port" => "string",
            "weight" => "float",
            'upc'=>'integer',
            'sku' => 'string',
            'material' => 'string',
            'factory_item' => 'string',
            'samples_requested' => 'string',
            'carton_size_l' => 'float',
            'carton_size_w' => 'float',
            'carton_size_h' => 'float',
            'factory_lead_time' => 'string',
        ];
    }

    public function setController($c)
    {
        $this->controller = $c;
    }

    public function getPoFields()
    {
        return $this->po_fields;
    }

    /**
     * list of all fields except user_id, supplier_id, and id
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

    public function getFieldType($field)
    {
        return isset($this->field_types[$field])?$this->field_types[$field]:"";
    }

    public function sanatizeProductArr($productArr)
    {
        foreach ($productArr as $field=>$value) {
            $field_type = $this->getFieldType($field);
            $productArr[$field] = $this->sanatize($field_type, $value);
        }
        return $productArr;
    }

    function createPurchaseOrderIfNotExists($purchaseOrderArr)
    {
        $product = PurchaseOrder::where(['buyer_id' => $purchaseOrderArr['buyer_id'],'supplier_id' => $purchaseOrderArr['supplier_id'], 'style' => $purchaseOrderArr['style']])->first();
        if (!$product) {
            $product = Product::create($purchaseOrderArr);
            return $product;
        }
        return $product;
    }

    function createProductIfNotExists($productArr)
    {
        $product = Product::where(['buyer_id' => $productArr['buyer_id'],'supplier_id' => $productArr['supplier_id'], 'style' => $productArr['style']])->first();
        if (!$product) {
            $product = Product::create($productArr);
            return $product;
        }
        return $product;
    }

    protected function sanatize($field_type, $value)
    {
        $field_type = strtolower(trim($field_type));
        $value = trim($value);
        switch ($field_type)
        {
            case "email":
                return filter_var($value, FILTER_SANITIZE_EMAIL);
                break;
            case "float":
                return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
                break;
            case "integer":case "int":
            return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            break;
            default:
                return $value;
        }
        return $value;
    }

    public function getProductIdFromFileName($name)
    {
        $name = trim($name);
        if (preg_match('/^([-\w]+)[^-\w]+/', $name, $matches)) {
            $style = $matches[1];
            $product = Product::where(['style' => $style])->first();
            if (!$product) return null;
            else return $product->product_id;
        }

        return null;
    }

    public function createRoom()
    {
        $url = "http://ec2-52-37-114-239.us-west-2.compute.amazonaws.com:8000/_";
        $json = ['snapshot' => "..."];

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, [
            'json' => $json,
        ]);

//        $res = $client->request('POST', 'https://api.github.com/user', [
//            'auth' => ['user', 'pass']
//        ]);
        if ($res->getStatusCode() == 201) {

            $uri = (string) $res->getBody();
            $uri = ltrim($uri, '/');
            return $uri;
        }
        else {
            throw new \Exception("Failed to fetch new ethercalc room");
        }
    }

    public function createTemplate($ethercalc_id, $templateID=0)
    {
        $url = "http://ec2-52-37-114-239.us-west-2.compute.amazonaws.com:8000/_/". $ethercalc_id;
        $json = $this->getCreateTemplateJsonBody();
        //dd(json_decode($json));
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, [
            'json' => json_decode($json),
        ]);

//        $uri = (string) $res->getBody();
//        return $uri;
        
        if ($res->getStatusCode() == 202) {
            $uri = (string) $res->getBody();
            //$uri = $res->getBody();
            return $uri;
        }
        else {
            throw new \Exception("Failed to fetch new ethercalc room");
        }

    }

    public function getNewWorksheet($purchase_order_id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($purchase_order_id);
        $ethercalc_id = $purchaseOrder->ethercalc_id;
        if (empty($ethercalc_id)) {
            $ethercalc_id = $this->createRoom();
            $purchaseOrder = PurchaseOrder::findOrFail($purchase_order_id);
            $purchaseOrder->ethercalc_id = $ethercalc_id;
            $purchaseOrder->save();
            $res = $this->createTemplate($ethercalc_id);
        }

        return $res;
    }


    private function getCreateTemplateJsonBody()
    {
        return file_get_contents(base_path('config/ethercalc_template.json'));
    }

}