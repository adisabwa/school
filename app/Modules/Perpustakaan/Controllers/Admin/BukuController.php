<?php

namespace Modules\Perpustakaan\Controllers\Admin;

use App\Controllers\BaseDataController;

class BukuController extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('BukuModel');
    }

}