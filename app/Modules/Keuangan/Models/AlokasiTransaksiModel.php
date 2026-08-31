<?php

namespace Modules\Keuangan\Models;

use App\Models\BaseModel;

class AlokasiTransaksiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'sch_keu_alokasi_transaksi';
        $this->relations = [
            'id_transaksi' => [
                'foreign_key' => 'id_iuran',
                'model' => 'Modules\Keuangan\Models\TransaksiModel',
                'selects' => [
                    'tanggal',
                    'keterangan',
                    'nominal_disetor',
                    'nominal_alokasi',
                ],
            ],
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
        ];
    }



}