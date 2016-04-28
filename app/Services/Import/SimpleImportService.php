<?php

namespace App\Services\Import;

use App\Product;
use App\Services\ProductService;

class SimpleImportService implements ImportServiceInterface
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
    
    function importSave($filename, $user_id, $supplier_id)
    {
        $products = $this->csv_to_array($filename);
        foreach ($products as $product) {
            $product['user_id'] = $user_id;
            $product['supplier_id'] = $supplier_id;
            print_r($product); echo "<br>";
            $this->productService->createProductIfNotExists($product);
        }
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