<?php

namespace Modules\Data\Controllers;

use App\Controllers\BaseDataController;

class PenghasilanController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('DataPenghasilanModel');
    }
}
