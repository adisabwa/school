<?php

namespace Modules\Perpustakaan\Models;

use App\Models\BaseModel;

class PeminjamanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch_lib_peminjaman';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->id; });
    }
}