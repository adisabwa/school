<?php

namespace Modules\Data\Controllers;

use Modules\Data\Controllers\BaseData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class Jabatan extends BaseData
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('JabatanModel');
    }
}
