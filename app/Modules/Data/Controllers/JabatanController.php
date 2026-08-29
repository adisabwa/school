<?php

namespace Modules\Data\Controllers;


use App\Controllers\BaseDataController;

class JabatanController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('DataJabatanModel');
    }
}
