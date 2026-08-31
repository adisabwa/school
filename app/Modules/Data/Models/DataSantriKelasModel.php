<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataSantriKelasModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__santri_kelas';
        $this->relations = [
          'id_kelas' => [
            'foreign_key' => 'id_kelas',
            'model' => 'DataKelasModel',
            // 'type' => 'left',
            'selects' => ['kelas','tingkat'],
          ],
          'id_kelas_ajar' => [
            'foreign_key' => 'id_kelas',
            'local_key' => 'id_kelas',
            'model' => 'DataKelasAjarModel',
            'alias' => 'sk_ka',
            'type' => 'left', 
            'selects' => ['nama_walas','nama_walas_lengkap', 'walas_signature','nama_walas_arab','id_jurusan','id_unit','nama_unit','nama_jurusan',
              'nama_kepala','nama_kepala_lengkap','nama_kepala_arab','kepala_signature','nbm_kepala'],
            'on_condition' => [
                '{n}'.PREFIX_TABLE.'_santri_kelas.tahun_ajaran=sk_ka.tahun_ajaran'
            ],
          ], 
          'id_santri' => [
            'foreign_key' => 'id_santri',
            'model' => 'DataSantriModel',
            // 'type' => 'left',
            'selects' => ['nama','status', 'nama_arab','nisn','stb','id_daerah','daerah','daerah_arab','induk_sekolah'],
          ]
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->kelas; });
    }
}