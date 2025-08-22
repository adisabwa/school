<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataUnitModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__unit';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->nama_unit; });
    }
}