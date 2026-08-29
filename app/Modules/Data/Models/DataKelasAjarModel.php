<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataKelasAjarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_kelas_ajar';
        $this->relations = [
          'id_kelas' => [
            'foreign_key' => 'id_kelas',
            'model' => 'DataKelasModel',
            'type' => 'left',
            'selects' => ['kelas','tingkat'],
          ],
          'id_walas' => [
            'foreign_key' => 'id_walas',
            'model' => 'DataGuruModel',
            'type' => 'left',
            'selects' => [
                "nama nama_walas",
                "prefix prefix_walas","suffix suffix_walas",
                "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_walas_lengkap",
                "nama_arab nama_walas_arab",
                "nbm nbm_walas",
                "signature walas_signature",
            ],
          ],
          'id_jurusan' => [
            'foreign_key' => 'id_jurusan',
            'model' => 'DataJurusanModel',
            'type' => 'left',
            'selects' => [
                'nama_jurusan','kode_jurusan',
            ],
          ],
          'id_unit' => [
            'foreign_key' => 'id_unit',
            'model' => 'DataUnitModel',
            'type' => 'left',
            'selects' => [
                'nama_unit','kode_unit','nama_kepala','nama_kepala_lengkap','nama_kepala_arab','kepala_signature','nbm_kepala',
            ],
          ],
          // 'id_santri' => [
          //   'foreign_key' => 'id',
          //   'local_key' => 'id_kelas',
          //   'table' => PREFIX_TABLE.'_santri_kelas',
          //   'type' => 'left',
          //   'selects' => ["{n}COUNT('.PREFIX_TABLE.'_santri_kelas.id) jumlah_santri" ],
          //   'on_condition' => [
          //     "'.PREFIX_TABLE.'_santri_kelas.id_kelas = {f}.id",
          //   ]
          // ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData(where: $where, 
        order: 'tingkat, kelas',
        concatFunc : function($d) { return "$d->kelas"; }, 
        addOptions : function($option, $data) { 
          $option->value = $data->id_kelas;
          $option->tingkat = $data->tingkat;
          $option->id_unit = $data->id_unit;
          $option->id_jurusan = $data->id_jurusan;
          return $option; 
        });
    }
}