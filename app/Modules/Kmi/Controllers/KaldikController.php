<?php

namespace Modules\Kmi\Controllers;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;

class KaldikController extends BaseDataController
{
    public $santriModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('KaldikModel');
    }


}