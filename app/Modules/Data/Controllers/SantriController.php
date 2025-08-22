<?php

namespace Modules\Data\Controllers;

use App\Controllers\BaseDataController;

class SantriController extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('DataSantriModel');
    }

    public function search()
    {
        $whereAnd = $this->request->getGet('and') ?? [];
        $whereOr = $this->request->getGet('or') ?? ['1=1'];
        $order = $this->request->getGet('order') ?? [];
        $order = implode(",", $order);

        $data = $this->model->getAll($whereAnd, $whereOr, $order)[0] ?? [];

        // var_dump($this->model->db->getLastQuery());
        return $this->respondCreated($data);
    }

    public function kelas()
    {
        return $this->respondCreated($this->model->getKelas());
    }
}