<?php

namespace Modules\Psb\Controllers;

use App\Controllers\BaseDataController;

class Psb extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('PsbModel');
        helper('psb');
    }

    public function search()
    {
        $whereAnd = $this->request->getGet('where') ?? [];
        $whereOr = $this->request->getGet('or') ?? ['id' => '-1'];
        // var_dump($whereAnd, $whereOr);
        $data = $this->model->getDataWhere(whereAnd: $whereAnd, whereOr: $whereOr);

        // var_dump($this->model->db->getLastQuery());
        return $this->respondCreated($data);
    }
    
    public function store()
    {
        $posts = $this->request->getPost();
        $id = $posts['id'];
        $data = $this->model->find($id);
        if (empty($data->no_pendaftaran)) {
            $posts['no_pendaftaran'] = getNomorPendaftaran();
        }
        $newRequest = $this->request->setGlobal('post', $posts);

        return parent::store();
    }

}