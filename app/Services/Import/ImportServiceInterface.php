<?php

namespace App\Services\Import;

interface ImportServiceInterface
{
    public function setController($c);
    public function importSave($filename, $user_id, $supplier_id);

}