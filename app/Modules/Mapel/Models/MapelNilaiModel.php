<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelNilaiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_nilai';
    }

}