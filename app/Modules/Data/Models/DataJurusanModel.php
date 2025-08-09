<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataJurusanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__jurusan';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return "$d->nama_jurusan"; });
    }
}