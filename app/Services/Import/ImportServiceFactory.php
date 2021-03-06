<?php

namespace App\Services\Import;

use App\Services\Import\BurlingtonImportService;
use App\Services\Import\SimpleImportService;
use App\Services\Import\InvalidArgumentException;


class ImportServiceFactory
{
    
    public static function create($type)
    {
        $type = strtolower(trim($type));
        switch ($type) {
            case 'berlington':
                return new BurlingtonImportService();
            case 'csv': case 'simple':
                return new SimpleImportService(); 
        }

        throw new InvalidArgumentException("Invalid Importer Type: $type");
    }
}