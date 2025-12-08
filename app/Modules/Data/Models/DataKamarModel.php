<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataKamarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__kamar';
        $this->relations = [
          'id_wali_kamar' => [
            'foreign_key' => 'id_wali_kamar',
            'model' => 'DataGuruModel',
            'type' => 'left',
            'selects' => [
                "nama nama_wamar",
                "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-','')) nama_wamar_lengkap",
                "nama_arab nama_wamar_arab",
                "nbm nbm_wamar",
                "signature wamar_signature",
              ],
          ]
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->rayon - $d->nomor"; });
    }
}