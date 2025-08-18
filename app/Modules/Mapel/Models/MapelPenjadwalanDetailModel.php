<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelPenjadwalanDetailModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_penjadwalan_detail';
        $this->relations = [
            'id_penjadwalan' => [
                'foreign_key' => 'id_penjadwalan',
                'table' => 'sch_aka_penjadwalan',
                'selects' => [
                    'id_semester', 'versi', 'tanggal',
                ]
            ],
            'id_sesi' => [
                'foreign_key' => 'id_sesi',
                'table' => 'sch__sesi',
                'selects' => [
                    'sesi','waktu_mulai','waktu_selesai',
                ]
            ],
            'id_pembagian_mapel' => [
                'foreign_key' => 'id_pembagian_mapel',
                'model' => 'MapelPembagianModel',
                'table' => 'sch_aka_pembagian_mapel',
                'selects' => [
                    'id_guru','id_mapel','id_kelas','kode_mapel',
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