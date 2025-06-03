<?php

namespace Modules\Data\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use App\Controllers\BaseDataController;

class AnggotaController extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('AnggotaModel');
    }
}