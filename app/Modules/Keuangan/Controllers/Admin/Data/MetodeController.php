<?php

namespace Modules\Keuangan\Controllers\Admin\Data;

use App\Controllers\BaseDataController;

class MetodeController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('Modules\Keuangan\Data\MetodeModel');

    }

    
}