<?php

namespace Modules\Keuangan\Models;

use App\Models\BaseModel;

class IuranSantriModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'keu_iuran_santri';
        $this->relations = [
            'id_iuran' => [
                'foreign_key' => 'id_iuran',
                'model' => 'Modules\Keuangan\Models\Data\IuranModel',
                'selects' => [
                    'nama_iuran',
                    'is_tunggakan',
                    'tipe',
                    'sasaran',
                    'id_pos',
                    'nama_pos',
                    'keterangan_pos',
                    'is_aktif_pos',
                    'id_unit',
                    'nama_unit',
                    'id_santri id_santri_tujuan',
                    'nama_santri_tujuan',
                    'kelas_santri_tujuan',
                    'kelas_tujuan'
                ],
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
                'model' => 'DataSantriModel',
                'alias' => 'santri_tujuan',
                'type' => 'left',
                'selects' => [
                    'nama',
                    'nama_arab',
                    'status status_santri',
                ]
            ],
            'kelas' => [
                'foreign_key' => 'id_santri',
                'local_key' => 'id_santri',
                'model' => 'DataSantriKelasModel',
                'alias' => 'kelas_santri_tujuan',
                'type' => 'left',
                'on_condition' => [
                    '{n}kelas_santri_tujuan.tahun_ajaran=alias_semester.tahun_ajaran'
                ],
                'selects' => [
                    'id_kelas',
                    'kelas',
                ]
            ],
            'iuran_santri' => [
                'foreign_key' => 'id',
                'local_key' => 'id_iuran_santri',
                'table' => 'sch_keu_alokasi_transaksi',
                'type' => 'left',
                'selects' => [
                    'id_transaksi',
                ],
            ],
        ];
    }



}