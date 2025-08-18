<?php

namespace Modules\Mapel\Models;

use App\Models\BaseModel;

class MapelPenjadwalanDetailModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_penjadwalan_detail';
    }

    public function getOptions($where = [])
    {
        return [];
      return $this->getOptionsData($where, function($d) { return $d->nama_mapel; });
    }
}