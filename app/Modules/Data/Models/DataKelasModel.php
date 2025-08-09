<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataKelasModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__kelas';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->kelas"; });
    }
}