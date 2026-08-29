<?php

namespace Modules\Keuangan\Controllers\Admin;

use App\Controllers\BaseDataController;

class IuranSaldoController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('Modules\Keuangan\Models\IuranSaldoModel');

    }

    public function get_saldo()
    {
        $id_santri = $this->request->getGetPost('id_santri');

        $this->model->selects = [
            "COALESCE(SUM(CASE WHEN {f}jenis_mutasi = 'in' THEN {f}nominal ELSE 0 END), 0) AS total_saldo_masuk",
            "COALESCE(SUM(CASE WHEN {f}jenis_mutasi = 'out' THEN {f}nominal ELSE 0 END), 0) AS total_saldo_keluar",
        ];

        $data = $this->model->getAll(whereAnd:[
            'id_santri' => $id_santri,
        ], groupBy: ['id_santri']);

        return $this->respondCreated($data);
    }
}