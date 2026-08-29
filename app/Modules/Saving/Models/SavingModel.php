<?php

namespace Modules\Saving\Models;

use App\Models\BaseModel;

class SavingModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'sav_tabungan';
        $this->relations = [
            'id_santri' => [
                'foreign_key' => 'id_santri',
                'model' => 'DataSantriModel',
                'selects' => ['nama nama_santri','id_kelas','kelas','tingkat'],
            ],
            'id_kas' => [
                'foreign_key' => 'id_kas',
                'model' => 'KasModel',
                'type' => 'left',
                'selects' => ['nama nama_kas'],
            ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama; });
    }
}