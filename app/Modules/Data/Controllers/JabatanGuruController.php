<?php

namespace Modules\Data\Controllers;


use App\Controllers\BaseDataController;

class JabatanGuruController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('DataJabatanGuruModel');
    }
}
