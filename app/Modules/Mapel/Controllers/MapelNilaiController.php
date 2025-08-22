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
        $id_pembagian_mapel = $this->request->getGetPost('id_pembagian_mapel') ?? -1;
        $pembagian = $this->mapelPembagianModel->find($id_pembagian_mapel) ?? NULL;
        $saved_nilai = $this->model->getAll(whereAnd: ['id_pembagian_mapel' => $id_pembagian_mapel]);
        // var_dump($pembagian);
        $id_kelas = $pembagian->id_kelas ?? -1;

        $santris = $this->santriModel->getAll(whereAnd: ['id_kelas' => $id_kelas], order: 'nama');
        $result = [];
        foreach ($saved_nilai as $key => $value) {
            $result[$value->id_santri] = $value;
        }
        
        // var_dump($santris, $result);
        array_walk($santris, function($a) use ($id_pembagian_mapel, $result) {
            $a->id_santri = $id_santri = $a->id;
            // var_dump($id_santri, $result[$id_santri]);
            $a->id = $result[$id_santri]->id ?? -1;
            $a->id_pembagian_mapel = $id_pembagian_mapel;
            $a->nilai = (object)[
                'nilai_harian' => $result[$id_santri]->nilai_harian ?? 0,
                'uts' => $result[$id_santri]->uts ?? 0,
                'uas' => $result[$id_santri]->uas ?? 0,
                'nilai_rapor' => $result[$id_santri]->nilai_rapor ?? 0,
                'katrol1' => $result[$id_santri]->katrol1 ?? 0,
                'katrol2' => $result[$id_santri]->katrol2 ?? 0,
            ];
            return $a;
        });

        return $this->respondCreated($santris);
    }
}