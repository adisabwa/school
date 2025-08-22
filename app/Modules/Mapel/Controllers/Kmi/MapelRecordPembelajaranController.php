<?php

namespace Modules\Mapel\Controllers\Kmi;

use App\Controllers\BaseDataController;

class MapelRecordPembelajaranController extends BaseDataController
{
    public $sesiModel;
    public $semesterModel;
    public $jadwalModel;
    public $jadwalDetailModel;

    public function __construct()
    {
        $this->model = model('MapelRecordPembelajaranModel');
        $this->sesiModel = model('DataSesiModel');
        $this->semesterModel = model('DataSemesterModel');
        $this->jadwalModel = model('MapelPenjadwalanModel');
        $this->jadwalDetailModel = model('MapelPenjadwalanDetailModel');
    }

    public function index()
    {
        $tanggal = date('Y-m-d');
        // $time = date('H:i');
        $time = '07:30';
        $semester_now = $this->semesterModel->get_semester_now();
        $check_sesi = $this->sesiModel->getDataWhere(whereAnd: [
            'waktu_mulai <= ' => $time,
            'waktu_selesai >= ' => $time,
        ]);
        // var_dump($semester_now, $check_sesi);
        $id_semester = $semester_now->id ?? -1;
        $id_sesi = $check_sesi->id ?? -1;
        $hari = get_hari($tanggal);
        $check_record = $this->model->addRelation([
            'id_sesi' => [
                'condiiton' => [
                    'waktu_mulai >= ' => $time,
                    'waktu_selesai <= ' => $time,
                ]
            ]
        ])->getAll(whereAnd: [
            'tanggal' => $tanggal,
            'id_semester' => $semester_now->id ?? -1,
        ]);

        // var_dump($check_record);
        if (empty($check_record)) {
            // var_dump('car');
            $this->input_data_from_detail($id_semester, $hari, $id_sesi);
        }
    }

    public function input_data_from_detail($id_semester, $hari, $id_sesi)
    {
        $jadwal = $this->jadwalModel->getDataWhere(whereAnd: [
            'id_semester' => $id_semester,
        ], order: 'tanggal desc');
        $id_penjadwalan = $jadwal->id ?? -1;
        $jadwal_detail = $this->jadwalDetailModel->getAll(whereAnd: [
            'hari' => $hari,
            'id_sesi' => $id_sesi,
            'id_penjadwalan' => $id_penjadwalan,
        ]);

        var_dump($jadwal_detail);

        // var_dump($id_semester, $hari, $id_sesi, $jadwal, $jadwal_detail, $this->jadwalDetailModel->getLastQuery());
    }

}