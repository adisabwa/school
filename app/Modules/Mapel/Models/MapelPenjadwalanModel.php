<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelPenjadwalanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_penjadwalan';
        $this->relations = [
            'id_sesi' => [
                'foreign_key' => 'id_sesi',
                'table' => 'sch__sesi',
                'selects' => [
                    'sesi','waktu_mulai','waktu_selesai',
                ]
            ],
            'sesi_akhir' => [
                'table' => 'sch__sesi',
                'alias' => 'sesi_akhir',
                'condition' => [
                    '{n}sesi_akhir.sesi=(sch__sesi.sesi + sch_aka_penjadwalan.jam - 1)'
                ],
                'selects' => [
                    'id id_sesi_akhir','sesi sesi_akhir','waktu_mulai waktu_mulai_akhir','waktu_selesai waktu_selesai_akhir',
                ]
            ],
            'id_pembagian_mapel' => [
                'foreign_key' => 'id_pembagian_mapel',
                'model' => 'MapelPembagianModel',
                // 'table' => 'sch_aka_pembagian_mapel',
                // 'type'  => 'left',
                'selects' => [
                    'id_guru','id_mapel','id_kelas','kode_mapel','kelas','nama_guru','nama_mapel',
                ]
            ]
        ];
    }

    public function getOptions($where = [])
    {
        return [];
      return $this->getOptionsData($where, function($d) { return $d->nama_mapel; });
    }
}