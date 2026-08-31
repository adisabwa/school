<?php

namespace Modules\Data\Controllers;

use App\Controllers\BaseDataController;

class SantriKelasController extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('DataSantriKelasModel');
    }

    public function getTotalSantri()
    {
        $semester = model('DataSemesterModel')->getSemesterNow();
        $this->model->selects = ["count({f}id_santri) as total_santri"];
        $total = $this->model->getAll(whereAnd:[
            'tahun_ajaran' => $semester->tahun_ajaran,
         ], groupBy: ['id_kelas'], order: 'tingkat, kelas'
        );
        
        return $this->respondCreated($total);
    }
}