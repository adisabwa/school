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
        $id_sesi = $this->request->getGet('id_sesi');
        $id_semester = $this->request->getGet('id_semester');
        $komplek = $this->request->getGet('id_komplek');
        // $time = date('H:i');
        $time = '07:30';
        // var_dump($semester_now, $check_sesi);

        $hari = get_hari($tanggal);
        $check_record = $this->model->getAll(whereAnd: [
            'id_sesi' => $id_sesi,
            'tanggal' => $tanggal,
            'id_semester' => $id_semester,
        ]);

        // var_dump($check_record);
        if (empty($check_record)) {
            // var_dump('car');
            $input = $this->input_data_from_detail($id_semester, $hari, $id_sesi, $tanggal);
        }

        $record = $this->model->addRelations([
            'id_kelas' => [
                'condition' => [
                    'komplek' => $komplek,
                ],
                'order' => 'kelas asc',
            ],
        ])->getAll(whereAnd: [
            'tanggal' => $tanggal,
            'id_sesi' => $id_sesi,
            'id_semester' => $id_semester,
        ], order: 'id desc');

        // var_dump($record);
        return $this->respondCreated($record);
    }

    public function input_data_from_detail($id_semester, $hari, $id_sesi, $tanggal)
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
        var_dump($this->jadwalDetailModel->getLastQuery());
        $datas = [];
        foreach ($jadwal_detail as $detail) {
            $datas[] = [
                'id_semester' => $id_semester,
                'id_sesi' => $id_sesi,
                'tanggal' => $tanggal,
                'id_kelas' => $detail->id_kelas,
                'id_mapel' => $detail->id_mapel,
                'id_guru' => $detail->id_guru,
                'kode_mapel' => $detail->kode_mapel,
            ];
        }

        $this->model->transBegin();

        if(!empty($datas)) {
            $this->model->insertBatch($datas);
        }

        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            return FALSE;
        } else {
            $this->model->transCommit();
            return TRUE;
        }
        // var_dump($id_semester, $hari, $id_sesi, $jadwal, $jadwal_detail, $this->jadwalDetailModel->getLastQuery());
    }

}