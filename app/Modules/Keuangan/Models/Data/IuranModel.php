<?php

namespace Modules\Keuangan\Models\Data;

use App\Models\BaseModel;

class IuranModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'keu_iuran';
        $this->relations = [
            'id_pos' => [
                'foreign_key' => 'id_pos',
                'model' => 'Modules\Keuangan\Models\Data\PosModel',
                'selects' => [
                    'nama_pos',
                    'keterangan keterangan_pos',
                    'is_aktif is_aktif_pos',
                ]
            ],
            'id_unit' => [
                'foreign_key' => 'id_unit',
                'model' => 'DataUnitModel',
                'type' => 'left',
                'selects' => [
                    'nama_unit'
                ]
            ],
            'id_semester' => [
                'foreign_key' => 'id_semester',
                'model' => 'DataSemesterModel',
                'alias' => 'alias_semester',
                'type' => 'left',
                'selects' => [
                    'semester','tahun_ajaran'
                ]
            ],
            'id_santri' => [
                'foreign_key' => 'id_santri',
                'local_key' => 'id_santri',
                'model' => 'DataSantriKelasModel',
                'alias' => 'santri_tujuan',
                'type' => 'left',
                'on_condition' => [
                    '{n}santri_tujuan.tahun_ajaran=alias_semester.tahun_ajaran'
                ],
                'selects' => [
                    'nama nama_santri_tujuan',
                    'kelas kelas_santri_tujuan',
                ]
            ],
            'id_kelas' => [
                'foreign_key' => 'id_kelas',
                'model' => 'DataKelasModel',
                'type' => 'left',
                'selects' => [
                    'kelas kelas_tujuan' 
                ]
            ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_iuran; });
    }
}