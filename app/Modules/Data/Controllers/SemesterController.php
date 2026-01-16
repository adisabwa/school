<?php

namespace Modules\Data\Controllers;


use App\Controllers\BaseDataController;

class SemesterController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('DataSemesterModel');
    }

    public function semester_now()
    {
        $data = $this->model->getSemesterNow();
        return $this->respond($data);
    }
}
