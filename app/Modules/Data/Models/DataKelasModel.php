<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataKelasModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_kelas';
        $this->relations = [
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
          $option->tingkat = $data->tingkat;
          return $option; 
        });
    }
}