<?php

namespace Modules\Saving\Controllers\Admin;

use App\Controllers\BaseDataController;

class KasController extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('KasModel');
    }

}