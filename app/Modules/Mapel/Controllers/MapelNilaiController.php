<?php

namespace Modules\Mapel\Controllers;

use App\Controllers\BaseDataController;

class MapelNilaiController extends BaseDataController
{
    public $santriModel;
    public $mapelPembagianModel;

    public function __construct()
    {
        $this->model = model('MapelNilaiModel');
        $this->santriModel = model('DataSantriModel');
        $this->mapelPembagianModel = model('MapelPembagianModel');
    }

    public function index()
    {
        $id_pembagian_mapel = $this->request->getGetPost('id_pembagian_mapel');
        $pembagian = $this->mapelPembagianModel->find($id_pembagian_mapel)[0] ?? NULL;
        $saved_nilai = $this->model->getAll(whereAnd: ['id_pembagian_mapel' => $id_pembagian_mapel]);
        // var_dump($pembagian);
        $id_kelas = $pembagian->id_kelas ?? NULL;

        $santris = $this->santriModel->getAll(whereAnd: ['id_kelas' => $id_kelas], order: 'nama');
        $saved_nilai = array_reduce($saved_nilai, function($el, $val){
            return $el[$val->id_santri] = $val;
        });

        array_walk($santris, function($a) use ($id_pembagian_mapel) {
            $a->id_pembagian_mapel = $id_pembagian_mapel;
            $a->nilai = (object)[
                'nilai_harian' => $saved_nilai[$a->id]->nilai_harian ?? 0,
                'uts' => $saved_nilai[$a->id]->uts ?? 0,
                'uas' => $saved_nilai[$a->id]->uas ?? 0,
                'katrol1' => $saved_nilai[$a->id]->katrol1 ?? 0,
                'katrol2' => $saved_nilai[$a->id]->katrol2 ?? 0,
            ];
            return $a;
        });

        return $this->respondCreated($santris);
    }
}