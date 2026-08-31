<?php

namespace Modules\Data\Controllers;


use App\Controllers\BaseDataController;

class SesiController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('DataSesiModel');
    }

    
    public function sesi_now()
    {
        $data = $this->model->getSesiNow();
        return $this->respond($data);
    }
}
