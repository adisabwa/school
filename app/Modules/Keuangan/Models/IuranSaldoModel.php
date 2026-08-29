<?php

namespace Modules\Keuangan\Models;

use App\Models\BaseModel;

class IuranSaldoModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'keu_saldo_iuran';
        $this->relations = [
            'id_transaksi' => [
                'foreign_key' => 'id_transaksi',
                'model' => 'Modules\Keuangan\Models\TransaksiModel',
                'selects' => [
                    'tanggal',
                    'keterangan',
                    'nominal_disetor',
                    'nominal_alokasi',
                ],
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
        ];
    }



}