<?php

namespace Modules\Keuangan\Controllers\Admin;

use App\Controllers\BaseDataController;

class AlokasiTransaksiController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('Modules\Keuangan\Models\AlokasiTransaksiModel');

    }

    
}