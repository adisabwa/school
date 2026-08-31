<?php

namespace Modules\Kmi\Models;

use App\Models\BaseModel;

class KaldikModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'aka_kaldik';
        $this->relations = [
          'id_unit' => [
            'foreign_key' => 'id_unit',
            'model' => 'DataUnitModel',
            'type' => 'left',
            'selects' => [
                'nama_unit','nama_kepala','nbm_kepala','kepala_signature','nama_kepala_lengkap','nama_kepala_arab',
              ],
          ]
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->keterangan; });
    }
}