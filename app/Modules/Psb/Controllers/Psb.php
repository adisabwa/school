<?php

namespace Modules\Psb\Controllers;

use App\Controllers\BaseDataController;

class Psb extends BaseDataController
{
    public function __construct()
    {
        
        $this->model = model('PsbModel');
    }

    public function search()
    {
        $whereAnd = $this->request->getGet('where') ?? [];
        $whereOr = $this->request->getGet('or') ?? ['1=2' => null];
        // var_dump($whereAnd, $whereOr);
        $data = $this->model->getDataWhere(whereAnd: $whereAnd, whereOr: $whereOr);

        // var_dump($this->model->db->getLastQuery());
        return $this->respondCreated($data);
    }

}