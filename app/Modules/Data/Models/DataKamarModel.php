<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataKamarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__kamar';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->rayon - $d->nomor"; });
    }
}