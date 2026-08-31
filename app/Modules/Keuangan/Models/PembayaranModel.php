<?php

namespace Modules\Keuangan\Models;

use App\Models\BaseModel;

class PembayaranModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'keu_pembayaran_santri';
        $this->relations = [
            'id_metode' => [
                'foreign_key' => 'id_metode',
                'model' => 'Modules\Keuangan\Data\MetodeModel',
                'selects' => [
                    'nama_metode',
                    'keterangan keterangan_metode',
                    'nama_kas',
                    'is_aktif is_aktif_metode',
                ]
            ],
            'id_santri' => [
                'foreign_key' => 'id_santri',
                'model' => 'DataSantriModel',
                'selects' => [
                    'nama',
                    'nama_arab',
                ]
            ],
        ];
    }



}