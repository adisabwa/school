<?php

namespace Modules\Keuangan\Models\Data;

use App\Models\BaseModel;

class MetodeModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'keu_metode';
        $this->relations = [
            'id_kas' => [
                'foreign_key' => 'id_kas',
                'model' => 'Modules\Keuangan\Models\Data\KasModel',
                'selects' => [
                    'nama_kas',
                    'keterangan keterangan_kas',
                ]
            ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, 
            function($d) { return $d->nama_metode; },
            function($option, $d) { 
                $option->id_kas = $d->id_kas;
                return $option;
            }
        );
    }

}