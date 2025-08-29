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
        $komplek = $this->request->getGet('komplek');
        $time = date('H:i');
        $time = '07:30';
        $tanggal = '2025-08-28';
        // var_dump($semester_now, $check_sesi);

        $hari = get_hari($tanggal);
        $check_record = $this->model->getAll(whereAnd: [
            'id_sesi' => $id_sesi,
            'tanggal' => $tanggal,
            'id_semester' => $id_semester,
        ]);

        // var_dump($this->model->getLastQuery());
        // var_dump($check_record);
        if (empty($check_record)) {
            // var_dump('car');
            $input = $this->input_data_from_detail($id_semester, $hari, $id_sesi, $tanggal);
        }

        $record = $this->model->addRelations([
            'id_kelas' => [
                'condition' => [
                    "komplek='$komplek'",
                ],
                'order' => 'kelas asc',
            ],
        ])->getAll(whereAnd: [
            'tanggal' => $tanggal,
            'id_sesi' => $id_sesi,
            'id_semester' => $id_semester,
        ], order: 'kelas asc, id desc');

        // var_dump($this->model->getLastQuery());
        return $this->respondCreated($record);
    }

    public function input_data_from_detail($id_semester, $hari, $id_sesi, $tanggal)
    {
        $jadwal = $this->jadwalModel->getDataWhere(whereAnd: [
            'id_semester' => $id_semester,
        ], order: 'tanggal desc');

        $sesi = $this->sesiModel->get($id_sesi);
        $sesi_before = ($sesi->sesi ?? 1) - 1;
        $check_before = $this->model->addRelations([
            'id_sesi' => [
                'condition' => [
                    "sesi=$sesi_before",
                ],
            ],
        ])->getAll(whereAnd: [
            'tanggal' => $tanggal,
            'id_semester' => $id_semester,
        ]);
        $check_before = $this->create_key($check_before);
        // var_dump($check_before);
        $id_penjadwalan = $jadwal->id ?? -1;
        $jadwal_detail = $this->jadwalDetailModel->getAll(whereAnd: [
            'hari' => $hari,
            'id_sesi' => $id_sesi,
            'id_penjadwalan' => $id_penjadwalan,
        ]);

        // var_dump($jadwal_detail);
        // var_dump($this->jadwalDetailModel->getLastQuery());
        $datas = [];
        foreach ($jadwal_detail as $detail) {
            $before = $check_before["$detail->id_kelas"] ?? [];
            $same = FALSE;
            if ($before) {
                if ($detail->id_guru == $before->id_guru && $detail->id_mapel == $before->id_mapel) {
                    $same = TRUE;
                }
            }
            $data = [];
            // var_dump($same);
            if ($same) {
                $data = [
                    'id_semester' => $id_semester,
                    'id_sesi' => $id_sesi,
                    'tanggal' => $tanggal,
                    'id_kelas' => $before->id_kelas,
                    'id_mapel' => $before->id_mapel,
                    'id_guru' => $before->id_guru,
                    'kode_mapel' => $before->kode_mapel,
                    'kehadiran' => $before->kehadiran,
                    'lainnya' => $before->lainnya,
                    'keterlambatan' => $before->keterlambatan,
                    'alasan_keterlambatan' => $before->alasan_keterlambatan,
                    'tugas' => $before->tugas,
                    'alasan' => $before->alasan,
                    'seragam' => $before->seragam,
                ];
            } else {
                $data = [
                    'id_semester' => $id_semester,
                    'id_sesi' => $id_sesi,
                    'tanggal' => $tanggal,
                    'id_kelas' => $detail->id_kelas,
                    'id_mapel' => $detail->id_mapel,
                    'id_guru' => $detail->id_guru,
                    'kode_mapel' => $detail->kode_mapel,
                    'kehadiran' => 'hadir',
                    'lainnya' => '',
                    'keterlambatan' => 0,
                    'alasan_keterlambatan' => '',
                    'tugas' => NULL,
                    'alasan' => '',
                    'seragam' => '1',
                ];
            }
            $datas[] = $data;
        }

        // var_dump($datas);
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

    public function create_key($data)
    {
        $datas = [];
        foreach($data as $d){
            $datas["$d->id_kelas"] = $d;
        }
        return $datas;
    }

}