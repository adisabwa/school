<?php

namespace Modules\Presensi\Controllers\Admin;

use App\Controllers\BaseDataController;

class PresensiSantriController extends BaseDataController
{
    public $sesiModel;
    public $semesterModel;
    public $jadwalModel;
    public $jadwalDetailModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('PresensiSantriModel');
        $this->santriModel = model('DataSantriKelasModel');
    }

    public function index($return = FALSE)
    {
        $id_mengajar_kelas = $this->request->getGetPost('id_mengajar_kelas');

        $dataMengajar = model('PresensiMengajarModel')->getData($id_mengajar_kelas);
        if (empty($dataMengajar)) exit('Tidak ada data');

        $saved_data = $this->model->getAll(whereAnd:[
            'id_mengajar_kelas'   => $id_mengajar_kelas,
        ]);
        // var_dump($dataMengajar, $saved_data);
        $result = [];
        foreach ($saved_data as $key => $value) {
            $result[$value->id_santri] = $value;
        }

        $santris = $this->santriModel->getAll(whereAnd: [
            '{f}id_kelas' => $dataMengajar->id_kelas,
            '{n}status' => '0',
            'tahun_ajaran' => $dataMengajar->tahun_ajaran,
        ], order: 'no_presensi asc, nama asc' );

        // var_dump($result);
        array_walk($santris, function($a) use ($dataMengajar, $result) {
            $a->id_santri = $id_santri = $a->id_santri;
            // var_dump($id_santri, $result[$id_santri]);
            $a->id = $result[$id_santri]->id ?? -1;
            $a->id_mengajar_kelas = $result[$id_santri]->id_mengajar_kelas ?? $dataMengajar->id;
            $a->kehadiran = $result[$id_santri]->kehadiran ?? 'hadir';
            $a->alasan = $result[$id_santri]->alasan ?? '';
            return $a;
        });

        return $this->respondCreated($santris);
    }

    public function getSummary()
    {
        $ids = $this->request->getGetPost('ids');
        $group_by = $this->request->getGetPost('group_by') ?? ['id_santri'];
        $ids = explode(',', $ids);
        if (empty($ids)) {
            $ids = [-1];
        }
        $this->model->selects = [
            'id_santri',
            'COUNT({f}.id) as sesi',
            'SUM({n}jam) as total_jam',
            'SUM(IF({f}.kehadiran="hadir",jam,0))  as total_hadir',
            'SUM(IF({f}.kehadiran="izin",jam,0))  as total_izin',
            'SUM(IF({f}.kehadiran="alfa",jam,0))  as total_alfa',
            'SUM(IF({f}.kehadiran="sakit",jam,0))  as total_sakit',
            'GROUP_CONCAT(DISTINCT {n}nama_mapel SEPARATOR ",") as detail_nama_mapel',
            'GROUP_CONCAT(DISTINCT {n}nama SEPARATOR ",") as detail_nama',
            'GROUP_CONCAT(IF({f}.kehadiran="hadir",jam,0) SEPARATOR ",") as detail_hadir',
            'GROUP_CONCAT(IF({f}.kehadiran="izin",jam,0) SEPARATOR ",") as detail_izin',
            'GROUP_CONCAT(IF({f}.kehadiran="alfa",jam,0) SEPARATOR ",") as detail_alfa',
            'GROUP_CONCAT(IF({f}.kehadiran="sakit",jam,0) SEPARATOR ",") as detail_sakit',
        ];
        $data = $this->model->getAll(whereIn: [
            'id_mengajar_kelas' => $ids,
        ], 
        order:'nama, nama_mapel',
        groupBy: $group_by);

        foreach ($data as $key => $value) {
            $value->presentase_hadir = round(($value->total_hadir / ($value->total_hadir + $value->total_izin + $value->total_alfa + $value->total_sakit)) * 100);
        }
        return $this->respondCreated($data);
    }

}