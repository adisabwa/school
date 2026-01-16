<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataSantriKamarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__santri_kamar';
        $this->relations = [
          'id_kamar' => [
            'foreign_key' => 'id_kamar',
            'model' => 'DataKamarModel',
            'type' => 'left',
            'selects' => ['rayon','rayon_arab','nomor','nama_wamar','nama_wamar_lengkap', 'wamar_signature','nama_wamar_arab',
            "{n}CONCAT(rayon,' - ',nomor) kamar" ],
          ],
          'id_santri' => [
            'foreign_key' => 'id_santri',
            'model' => 'DataSantriModel',
            'type' => 'left',
            'selects' => ['nama','nama_arab','nisn','stb','kelas','id_kelas','id_daerah','daerah'],
          ]
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->kamar; });
    }
}