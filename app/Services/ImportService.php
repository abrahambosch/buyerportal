<?php

namespace App\Services;

use App\Product;

class ImportService
{
    protected $controller = null;
    
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
            Product::create($productArr);
        }
    }

    function berlingtonImportSave($filename, $user_id, $seller_id)
    {
        $products = $this->csv_to_array($filename);
        foreach ($products as $product) {
            $product['user_id'] = $user_id;
            $product['seller_id'] = $seller_id;
            print_r($product); echo "<br>";
            $this->createProductIfNotExists($product);
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