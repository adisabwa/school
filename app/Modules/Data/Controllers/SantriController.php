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

    public function options_kelas()
    {
        $tahun_ajaran = $this->request->getGet('tahun_ajaran');
        $this->model->selects = ['group_concat({f}id) id_santris'];
        $data = $this->model->getDataWhere(whereAnd: [
            '{n}tahun_ajaran' => $tahun_ajaran,
        ], order: 'kelas ASC');
        // var_dump($this->model->getLastQuery());
        $this->model = $this->model->reset();

        $ids = empty($data->id_santris) ? ['1=1'] : ["{f}.id NOT IN ($data->id_santris)" => NULL];
        $whereAnd = array_merge($ids, [
            'status' => '0',
            'nama !=' => '',
        ]);
        // var_dump($whereAnd);
        $this->model->selects = [];
        return $this->respondCreated($this->model->getOptions(where: $whereAnd));
    }
}