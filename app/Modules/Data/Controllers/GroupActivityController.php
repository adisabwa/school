<?php

namespace Modules\Data\Controllers;


use App\Controllers\BaseDataController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class GroupActivityController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('GroupActivityModel');
    }
}
