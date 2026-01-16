<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataKelasModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__kelas';
        $this->relations = [
          'id_walas' => [
            'foreign_key' => 'id_walas',
            'model' => 'DataGuruModel',
            'type' => 'left',
            'selects' => [
                "nama nama_walas",
                "prefix prefix_walas","suffix suffix_walas",
                "TRIM(REPLACE(CONCAT(COALESCE({f}.prefix,''),{f}.nama,COALESCE({f}.suffix,'')),'-',''))  nama_walas_lengkap",
                "nama_arab nama_walas_arab",
                "nbm nbm_walas",
                "signature walas_signature",
            ],
          ],
          'id_santri' => [
            'foreign_key' => 'id',
            'local_key' => 'id_kelas',
            'table' => 'sch__santri',
            'type' => 'left',
            'selects' => ["{n}COUNT(sch__santri.id) jumlah_santri" ],
            'group_by' => ['{n}id_kelas'],
            'condition' => [
              "status='0'",
            ]
          ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->kelas"; });
    }
}