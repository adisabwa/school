<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelPenjadwalanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'aka_penjadwalan';
        $this->relations = [
            'id_sesi' => [
                'foreign_key' => 'id_sesi',
                'table' => PREFIX_TABLE.'_sesi',
                'selects' => [
                    'sesi sesi_awal', 'sesi','waktu_mulai','waktu_selesai',
                ]
            ],
            'sesi_akhir' => [
                'table' => PREFIX_TABLE.'_sesi',
                'alias' => 'sesi_akhir',
                'on_condition' => [
                    '{n}sesi_akhir.sesi=('.PREFIX_TABLE.'_sesi.sesi + '.PREFIX_TABLE.'aka_penjadwalan.jam - 1)'
                ],
                'selects' => [
                    'id id_sesi_akhir','sesi sesi_akhir','waktu_mulai waktu_mulai_akhir','waktu_selesai waktu_selesai_akhir',
                ]
            ],
            'id_pembagian_mapel' => [
                'foreign_key' => 'id_pembagian_mapel',
                'model' => 'MapelPembagianModel',
                // 'table' => PREFIX_TABLE.'aka_pembagian_mapel',
                // 'type'  => 'left',
                'selects' => [
                    'id_guru','id_mapel','id_kelas', 'id_kelas id_kelas_pembagian','kode_mapel','kelas','nama_guru','nama_mapel','no_hp_guru','nama_guru_lengkap',
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