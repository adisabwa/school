<?php

namespace Modules\Kmi\Models;

use App\Models\BaseModel;

class KaldikModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_aka_kaldik';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->keterangan; });
    }
}