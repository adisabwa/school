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
        $order = $this->request->getGetPost('order') ?? ['nama asc'];
        $order = implode(',', $order);
        $pembagian = $this->mapelPembagianModel->find($id_pembagian_mapel) ?? NULL;
        $saved_nilai = $this->model->getAll(whereAnd: ['id_pembagian_mapel' => $id_pembagian_mapel]);
        // var_dump($pembagian);
        $id_kelas = $pembagian->id_kelas ?? -1;

        $santris = $this->santriModel->getAll(whereAnd: ['id_kelas' => $id_kelas, 'status' => '0'], order: $order);
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

    public function rekapitulasi()
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        if (empty($id_semester)) $id_semester = -1;
        if (empty($id_kelas)) $id_kelas = -1;

        $order = $this->request->getGetPost('order') ?? ['nama asc'];
        $order = implode(',', $order);

        // var_dump($id_kelas, $id_semester);
        $mapel = $this->mapelPembagianModel->getAll(whereAnd: ['id_kelas' => $id_kelas, 'id_semester' => $id_semester], order: 'nama_mapel asc');
        $mapels = [];
        // $mapel = array_splice($mapel, 0, 15);
        foreach ($mapel as $key => $value) {
            $mapels[$value->id] = (object) [
                'id_pembagian_mapel' => $value->id,
                'nama_mapel' => $value->nama_mapel,
                'uts' => 0,
                'uas' => 0,
                'nilai_rapor' => 0,
                'katrol1' => 0,
            ];
        }
        // var_dump($mapels);exit;
        $santris = $this->santriModel->getAll(whereAnd: ['id_kelas' => $id_kelas, 'status' => '0'], order: $order);
        // var_dump($this->santriModel->getLastQuery());
        $result = [];
        foreach ($santris as $key => $santri) {
            $result[$santri->id] = (object) [
                'id_santri' => $santri->id,
                'nama' => $santri->nama,
                'mapel' => unserialize(serialize($mapels)),
            ];
        }

        $saved_nilai = $this->model->getAll(whereAnd: ['{n}id_kelas' => $id_kelas]);
        foreach ($saved_nilai as $key => $nilai) {
            $id_santri = $nilai->id_santri;
            $id_pembagian_mapel = $nilai->id_pembagian_mapel;
            // var_dump($id_santri, $id_pembagian_mapel, $result[$id_santri]->nama, $nilai->uts);
            if(isset($result[$id_santri]) && isset($result[$id_santri]->mapel[$id_pembagian_mapel])){
                $result[$id_santri]->mapel[$id_pembagian_mapel]->id = $nilai->id;
                $result[$id_santri]->mapel[$id_pembagian_mapel]->uts = $nilai->uts;
                $result[$id_santri]->mapel[$id_pembagian_mapel]->uas = $nilai->uas;
                $result[$id_santri]->mapel[$id_pembagian_mapel]->nilai_rapor = $nilai->nilai_rapor;
                $result[$id_santri]->mapel[$id_pembagian_mapel]->katrol1 = $nilai->katrol1;
                $result[$id_santri]->mapel[$id_pembagian_mapel]->katrol2 = $nilai->katrol2;
            }
        }

        return $this->respondCreated(array_values($result));
    }
}