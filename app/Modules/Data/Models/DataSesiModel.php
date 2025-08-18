<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataSesiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__sesi';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->sesi; });
    }
}