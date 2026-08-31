<?php

namespace Modules\Keuangan\Models;

use App\Models\BaseModel;

class TransaksiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'keu_transaksi';
        $this->relations = [
            'id_metode' => [
                'foreign_key' => 'id_metode',
                'model' => 'Modules\Keuangan\Data\MetodeModel',
                'type' => 'left',
                'selects' => [
                    'nama_metode',
                    'keterangan keterangan_metode',
                    'is_aktif is_aktif_metode',
                    'id_kas',
                    'nama_kas',
                    'keterangan_kas',
                ]
            ],
            'id_pos' => [
                'foreign_key' => 'id_pos',
                'model' => 'Modules\Keuangan\Models\Data\PosModel',
                'type' => 'left',
                'selects' => [
                    'nama_pos',
                    'keterangan keterangan_pos',
                    'is_aktif is_aktif_pos',
                ]
            ],
            'id_kategori' => [
                'foreign_key' => 'id_kategori',
                'model' => 'Modules\Keuangan\Models\Data\KategoriModel',
                'type' => 'left',
                'selects' => [
                    'nama_kategori',
                ]
            ],
            'id_santri' => [
                'foreign_key' => 'id_santri',
                'model' => 'DataSantriModel',
                'type' => 'left',
                'selects' => [
                    'nama',
                    'nama_arab',
                    'status status_santri',
                    'id_kelas',
                    'kelas',
                ]
            ],
        ];
    }



}