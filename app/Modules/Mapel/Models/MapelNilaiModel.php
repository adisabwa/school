<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelNilaiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_nilai';
        $this->relations = [
            'id_pembagian_mapel' => [
                'foreign_key' => 'id_pembagian_mapel',
                'model' => 'MapelPembagianModel',
                'selects' => [
                    '*',
                    'id id_pembagian_mapel' // Alias to avoid conflict
                ]
            ],
        ];
    }

}