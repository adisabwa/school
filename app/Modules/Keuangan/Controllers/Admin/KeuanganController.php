<?php

namespace Modules\Keuangan\Controllers\Admin;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory as IOFactorySpreadsheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory as IOFactoryWord;

class KeuanganController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('KeuanganModel');

    }

    
}