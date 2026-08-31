<?php

namespace Modules\Perpustakaan\Controllers\Admin;

use App\Controllers\BaseDataController;

class PeminjamanController extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('PeminjamanModel');
    }

}