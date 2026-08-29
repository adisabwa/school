<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataUnitModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_unit';
        $this->relations = [
          'id_kepala' => [
            'foreign_key' => 'id_kepala',
            'model' => 'DataGuruModel',
            'type' => 'left',
            'selects' => [
                "nama nama_kepala",
                "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-','')) nama_kepala_lengkap",
                "nama_arab nama_kepala_arab",
                "nbm nbm_kepala",
                "signature kepala_signature",
              ],
          ]
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_unit; });
    }
}