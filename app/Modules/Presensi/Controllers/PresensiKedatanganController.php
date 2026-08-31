<?php

namespace Modules\Presensi\Controllers;

use App\Controllers\BaseDataController;
use App\Libraries\NotificationManager;

class PresensiKedatanganController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('PresensiKedatanganModel');
    }

    public function getSummary()
    {
        
        $where = $this->request->getGetPost('where') ?? ['1=2'];
        $order = $this->request->getGetPost('order') ?? [];
        $order = implode(',', $order);
        
        $this->model->selects = [
            "{n}COUNT(IF(telat_datang='1',1,NULL)) total_telat_datang",
            "{n}COUNT(IF(telat_pulang='1',1,NULL)) total_telat_pulang",
            "COUNT({f}id) total_kehadiran",

        ];
        $data = $this->model->getAll(whereAnd: $where, groupBy: ['id_guru'], order: $order);

        return $this->respondCreated(array_values($data));
    }
}