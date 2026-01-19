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
        $this->santriModel = model('DataSantriModel');
    }

    public function index($return = FALSE)
    {
        $id_mengajar_kelas = $this->request->getGetPost('id_mengajar_kelas');

        $dataMengajar = model('PresensiMengajarModel')->getData($id_mengajar_kelas);
        if (empty($dataMengajar)) exit('Tidak ada data');

        $saved_data = $this->model->getAll(whereAnd:[
            'id_mengajar_kelas'   => $id_mengajar_kelas,
        ]);
        // var_dump($saved_data);
        $result = [];
        foreach ($saved_data as $key => $value) {
            $result[$value->id_santri] = $value;
        }

        $santris = $this->santriModel->getAll(whereAnd: [
            'id_kelas' => $dataMengajar->id_kelas,
        ], order: 'no_presensi asc, nama asc' );

        // var_dump($result);
        array_walk($santris, function($a) use ($dataMengajar, $result) {
            $a->id_santri = $id_santri = $a->id;
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
        $ids = explode(',', $ids);
        if (empty($ids)) {
            $ids = [-1];
        }
        $this->model->selects = [
            'id_santri',
            'COUNT(IF({f}.kehadiran="hadir",1,NULL)) * jam as total_hadir',
            'COUNT(IF({f}.kehadiran="izin",1,NULL)) * jam as total_izin',
            'COUNT(IF({f}.kehadiran="alfa",1,NULL)) * jam as total_alfa',
            'COUNT(IF({f}.kehadiran="sakit",1,NULL)) * jam as total_sakit',
        ];
        $data = $this->model->getAll(whereIn: [
            'id_mengajar_kelas' => $ids,
        ], groupBy: ['id_santri']);

        foreach ($data as $key => $value) {
            $value->presentase_hadir = round(($value->total_hadir / ($value->total_hadir + $value->total_izin + $value->total_alfa + $value->total_sakit)) * 100);
        }
        return $this->respondCreated($data);
    }

}