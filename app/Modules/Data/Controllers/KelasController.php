<?php

namespace Modules\Data\Controllers;


use App\Controllers\BaseDataController;

class KelasController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('DataKelasModel');
    }
}
