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
        // var_dump($this->model->getLastQuery(), $this->model->getTableName());
        return $this->respond($data);
    }

    public function options_tahun_ajaran()
    {
        $where = $this->request->getGet('where') ?? [];
        $data = $this->model->getOptionsTahunAjaran($where);
        return $this->respond($data);
    }
}
